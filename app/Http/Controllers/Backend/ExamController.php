<?php

namespace App\Http\Controllers\Backend;

use App\Models\Exam;
use App\Models\ClassRoom;
use App\Models\ExamResult;
use Illuminate\Http\Request;
use App\Services\ActivityService;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class ExamController extends Controller
{
    /**
     * Display a listing of the exams.
     */
    public function index()
    {
        $user = Auth::user();
        $exams = [];

        if ($user->role === 1) { // Admin
            $exams = Exam::with(['teacher', 'class'])
                ->latest()
                ->get();
        } elseif ($user->role === 2) { // Teacher
            $exams = Exam::where('teacher_id', $user->id)
                ->with(['class'])
                ->latest()
                ->get();
        } elseif (in_array($user->role, [3, 4])) { // Parent or Student
            $exams = Exam::whereHas('class.students', function ($query) use ($user) {
                $query->where('users.id', $user->role === 3 ? $user->parent_id : $user->id);
            })
                ->with(['teacher', 'class'])
                ->latest()
                ->get();
        }

        return view('backend.pages.exams.index', compact('exams'));
    }

    /**
     * Show the form for creating a new exam.
     */
    public function create()
    {
        $user = Auth::user();
        $classes = $user->role === 1 ? ClassRoom::all() : ClassRoom::where('teacher_id', $user->id)->get();

        return view('backend.pages.exams.create', compact('classes'));
    }

    /**
     * Store a newly created exam in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'class_id' => 'required|exists:class_rooms,id',
            'subject' => 'required|string|max:255',
            'exam_date' => 'required|date|after_or_equal:today',
            'start_time' => 'required',
            'end_time' => [
                'required',
                function ($attribute, $value, $fail) use ($request) {
                    $startTime = strtotime($request->start_time);
                    $endTime = strtotime($value);

                    if ($startTime >= $endTime) {
                        $fail('The end time must be after the start time.');
                    }
                }
            ],
            'total_marks' => 'required|integer|min:1',
            'passing_marks' => 'required|integer|min:1|lte:total_marks',
            'type' => 'required|in:first_term,second_term,third_term,final_term',
            'description' => 'nullable|string',
            'instructions' => 'nullable|string'
        ]);

        $exam = Exam::create([
            'teacher_id' => Auth::id(),
            'status' => Exam::STATUS_SCHEDULED,
            ...$request->all()
        ]);

        ActivityService::log('Created a new exam' , Auth::id() , 'exam');

        return redirect()->route('exams.index')
            ->with('success', 'Exam created successfully.');
    }

    /**
     * Display the specified exam.
     */
    public function show(Exam $exam)
    {
        $exam->load(['teacher', 'class', 'results.student']);
        return view('backend.pages.exams.show', compact('exam'));
    }

    /**
     * Show the form for editing the specified exam.
     */
    public function edit(Exam $exam)
    {
        if (Auth::id() !== $exam->teacher_id && Auth::user()->role !== 1) {
            return redirect()->route('exams.index')
                ->with('error', 'You are not authorized to edit this quiz.');
        }

        $classes = ClassRoom::where('teacher_id', Auth::id())->get();
        return view('backend.pages.exams.edit', compact('exam', 'classes'));
    }

    /**
     * Update the specified exam in storage.
     */
    public function update(Request $request, Exam $exam)
    {
        if (Auth::id() !== $exam->teacher_id && Auth::user()->role !== 1) {
            return redirect()->route('exams.index')
                ->with('error', 'You are not authorized to update this exam.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'class_id' => 'required|exists:class_rooms,id',
            'subject' => 'required|string|max:255',
            'exam_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => [
                'required',
                function ($attribute, $value, $fail) use ($request) {
                    $startTime = strtotime($request->start_time);
                    $endTime = strtotime($value);

                    if ($startTime >= $endTime) {
                        $fail('The end time must be after the start time.');
                    }
                }
            ],
            'total_marks' => 'required|integer|min:1',
            'passing_marks' => 'required|integer|min:1|lte:total_marks',
            'type' => 'required|in:first_term,second_term,third_term,final_term',
            'description' => 'nullable|string',
            'instructions' => 'nullable|string',
            'status' => 'required|in:' . implode(',', array_keys(Exam::getStatuses()))
        ]);

        $exam->update($request->all());
        ActivityService::log('Updated an exam' , Auth::id() , 'exam');

        return redirect()->route('exams.index')
            ->with('success', 'Exam updated successfully.');
    }

    /**
     * Remove the specified exam from storage.
     */
    public function destroy(Exam $exam)
    {
        if (Auth::id() !== $exam->teacher_id && Auth::user()->role !== 1) {
            return redirect()->route('exams.index')
                ->with('error', 'You are not authorized to delete this quiz.');
        }

        $exam->delete();
        ActivityService::log('Deleted an exam' , Auth::id() , 'exam');
        return redirect()->route('exams.index')
            ->with('success', 'Quiz deleted successfully.');
    }

    /**
     * Show the form for managing exam results.
     */
    public function results(Exam $exam)
    {
        $exam->load(['class.students', 'results']);
        return view('backend.pages.exams.results', compact('exam'));
    }

    /**
     * Store exam results.
     */
    public function storeResults(Request $request, Exam $exam)
    {
        $request->validate([
            'results' => 'required|array',
            'results.*.student_id' => 'required|exists:users,id',
            'results.*.marks_obtained' => 'required|integer|min:0|max:' . $exam->total_marks,
            'results.*.remarks' => 'nullable|string|max:255',
        ]);

        foreach ($request->results as $result) {
            $marksObtained = $result['marks_obtained'];
            $percentage = ($marksObtained / $exam->total_marks) * 100;
            $isPassed = $marksObtained >= $exam->passing_marks;

            // Calculate grade based on percentage
            $grade = 'F';
            if ($percentage >= 90) {
                $grade = 'A+';
            } elseif ($percentage >= 80) {
                $grade = 'A';
            } elseif ($percentage >= 70) {
                $grade = 'B+';
            } elseif ($percentage >= 60) {
                $grade = 'B';
            } elseif ($percentage >= 50) {
                $grade = 'C+';
            } elseif ($percentage >= 40) {
                $grade = 'C';
            } elseif ($percentage >= 33) {
                $grade = 'D';
            }

            $exam->results()->updateOrCreate(
                ['student_id' => $result['student_id']],
                [
                    'marks_obtained' => $marksObtained,
                    'percentage' => $percentage,
                    'grade' => $grade,
                    'is_passed' => $isPassed,
                    'remarks' => $result['remarks'] ?? null,
                ]
            );
        }

        // Update exam status to completed
        $exam->update(['status' => 'completed']);

        ActivityService::log('Updated exam results', Auth::id(), 'exam');

        return redirect()->route('exams.show', $exam)
            ->with('success', 'Exam results have been saved successfully.');
    }

    /**
     * Display exam reports.
     */
    public function reports()
    {
        $user = Auth::user();
        $exams = [];

        if ($user->role === 1) { // Admin
            $exams = Exam::with(['teacher', 'class', 'results.student'])
                ->where('status', Exam::STATUS_COMPLETED)
                ->latest()
                ->get();
        } elseif ($user->role === 2) { // Teacher
            $exams = Exam::where('teacher_id', $user->id)
                ->with(['class', 'results.student'])
                ->where('status', Exam::STATUS_COMPLETED)
                ->latest()
                ->get();
        } else {
            return redirect()->route('exams.index')
                ->with('error', 'You are not authorized to view exam reports.');
        }

        // Calculate statistics
        $statistics = [
            'total_exams' => $exams->count(),
            'total_students' => $exams->flatMap->results->unique('student_id')->count(),
            'average_pass_rate' => $this->calculateAveragePassRate($exams),
            'average_score' => $exams->flatMap->results->avg('percentage') ?? 0,
        ];


        return view('backend.pages.exams.reports', compact('exams', 'statistics'));
    }

    public function printReports()
    {
        $exams = Exam::with(['class', 'results'])->get();
        $pdf = PDF::loadView('backend.pages.exams.print-reports', compact('exams'));
        return $pdf->download('exam-reports.pdf');
    }

    public function printResults(Exam $exam)
    {
        $exam->load(['class', 'results.student']);
        $pdf = PDF::loadView('backend.pages.exams.print-results', compact('exam'));
        return $pdf->download("exam-results-{$exam->title}.pdf");
    }

    /**
     * Calculate average pass rate across all exams
     */
    private function calculateAveragePassRate($exams)
    {
        $examsWithResults = $exams->filter(function ($exam) {
            return $exam->results->isNotEmpty();
        });

        if ($examsWithResults->isEmpty()) {
            return 0;
        }

        $totalPassRate = $examsWithResults->sum(function ($exam) {
            return ($exam->results->where('is_passed', true)->count() / $exam->results->count()) * 100;
        });

        return $totalPassRate / $examsWithResults->count();
    }

    /**
     * Calculate grade based on percentage
     */
    private function calculateGrade($percentage)
    {
        if ($percentage >= 90) return 'A+';
        if ($percentage >= 80) return 'A';
        if ($percentage >= 75) return 'B+';
        if ($percentage >= 70) return 'B';
        if ($percentage >= 65) return 'C+';
        if ($percentage >= 60) return 'C';
        if ($percentage >= 50) return 'D';
        return 'F';
    }
}
