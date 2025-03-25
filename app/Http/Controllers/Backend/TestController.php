<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamResult;
use App\Models\ClassRoom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TestController extends Controller
{
    /**
     * Display a listing of the tests.
     */
    public function index()
    {
        $user = Auth::user();
        $tests = [];

        if ($user->role === 1) { // Admin
            $tests = Exam::whereIn('type', ['normal', 'weekly', 'monthly', 'yearly'])
                ->with(['teacher', 'class'])
                ->latest()
                ->get();
        } elseif ($user->role === 2) { // Teacher
            $tests = Exam::where('teacher_id', $user->id)
                ->whereIn('type', ['normal', 'weekly', 'monthly', 'yearly'])
                ->with(['class'])
                ->latest()
                ->get();
        } elseif (in_array($user->role, [3, 4])) { // Parent or Student
            $tests = Exam::whereHas('class.students', function ($query) use ($user) {
                $query->where('users.id', $user->role === 3 ? $user->parent_id : $user->id);
            })
                ->whereIn('type', ['normal', 'weekly', 'monthly', 'yearly'])
                ->with(['teacher', 'class'])
                ->latest()
                ->get();
        }

        return view('backend.pages.tests.index', compact('tests'));
    }

    /**
     * Show the form for creating a new test.
     */
    public function create()
    {
        $user = Auth::user();
        $classes = $user->role === 1 ? ClassRoom::all() : ClassRoom::where('teacher_id', $user->id)->get();
        return view('backend.pages.tests.create', compact('classes'));
    }

    /**
     * Store a newly created test in storage.
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
            'type' => 'required|in:normal,weekly,monthly,yearly',
            'description' => 'nullable|string',
            'instructions' => 'nullable|string'
        ]);

        $test = Exam::create([
            'teacher_id' => Auth::id(),
            'status' => 'scheduled',
            ...$request->all()
        ]);

        return redirect()->route('tests.index')
            ->with('success', 'Test created successfully.');
    }

    /**
     * Display the specified test.
     */
    public function show(Exam $test)
    {
        $test->load(['teacher', 'class', 'results.student']);
        return view('backend.pages.tests.show', compact('test'));
    }

    /**
     * Show the form for editing the specified test.
     */
    public function edit(Exam $test)
    {
        if (Auth::user()->role !== 1 && Auth::id() !== $test->teacher_id) {
            return redirect()->route('tests.index')
                ->with('error', 'You are not authorized to edit this test.');
        }

        $classes = Auth::user()->role === 1 ? ClassRoom::all() : ClassRoom::where('teacher_id', Auth::id())->get();
        return view('backend.pages.tests.edit', compact('test', 'classes'));
    }

    /**
     * Update the specified test in storage.
     */
    public function update(Request $request, Exam $test)
    {
        if (Auth::user()->role !== 1 && Auth::id() !== $test->teacher_id) {
            return redirect()->route('tests.index')
                ->with('error', 'You are not authorized to update this test.');
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
            'type' => 'required|in:normal,weekly,monthly,yearly',
            'description' => 'nullable|string',
            'instructions' => 'nullable|string',
            'status' => 'required|in:scheduled,in_progress,completed,cancelled'
        ]);

        $test->update($request->all());

        return redirect()->route('tests.index')
            ->with('success', 'Test updated successfully.');
    }

    /**
     * Remove the specified test from storage.
     */
    public function destroy(Exam $test)
    {
        if (Auth::user()->role !== 1 && Auth::id() !== $test->teacher_id) {
            return redirect()->route('tests.index')
                ->with('error', 'You are not authorized to delete this test.');
        }

        $test->delete();

        return redirect()->route('tests.index')
            ->with('success', 'Test deleted successfully.');
    }

    /**
     * Show test results form.
     */
    public function results(Exam $test)
    {
        if (Auth::user()->role !== 1 && Auth::id() !== $test->teacher_id) {
            return redirect()->route('tests.index')
                ->with('error', 'You are not authorized to manage results for this test.');
        }

        $test->load(['class.students', 'results.student']);
        return view('backend.pages.tests.results', compact('test'));
    }

    /**
     * Store test results.
     */
    public function storeResults(Request $request, Exam $test)
    {
        if (Auth::user()->role !== 1 && Auth::id() !== $test->teacher_id) {
            return redirect()->route('tests.index')
                ->with('error', 'You are not authorized to manage results for this test.');
        }

        $request->validate([
            'results.*.student_id' => 'required|exists:users,id',
            'results.*.marks_obtained' => 'required|integer|min:0|max:' . $test->total_marks,
            'results.*.remarks' => 'nullable|string'
        ]);

        DB::transaction(function () use ($request, $test) {
            foreach ($request->results as $result) {
                $percentage = ($result['marks_obtained'] / $test->total_marks) * 100;

                ExamResult::updateOrCreate(
                    [
                        'exam_id' => $test->id,
                        'student_id' => $result['student_id']
                    ],
                    [
                        'marks_obtained' => $result['marks_obtained'],
                        'percentage' => $percentage,
                        'grade' => $this->calculateGrade($percentage),
                        'remarks' => $result['remarks'] ?? null,
                        'is_passed' => $result['marks_obtained'] >= $test->passing_marks
                    ]
                );
            }

            $test->update(['status' => 'completed']);
        });

        return redirect()->route('tests.show', $test)
            ->with('success', 'Test results have been recorded successfully.');
    }

    /**
     * Display test reports.
     */
    public function reports()
    {
        $user = auth()->user();
        $query = Exam::with(['class', 'results']);

        // Apply filters
        if (request('class_id')) {
            $query->where('class_id', request('class_id'));
        }

        if (request('type')) {
            $query->where('type', request('type'));
        }

        if (request('start_date')) {
            $query->where('exam_date', '>=', request('start_date'));
        }

        if (request('end_date')) {
            $query->where('exam_date', '<=', request('end_date'));
        }

        // Filter based on user role
        if ($user->role === 2) { // Teacher
            $query->whereHas('class', function ($q) use ($user) {
                $q->whereHas('timetables', function ($q) use ($user) {
                    $q->where('teacher_id', $user->id);
                });
            });
        }

        $tests = $query->get();

        // Calculate statistics
        $totalTests = $tests->count();
        $totalStudents = $tests->flatMap->results->unique('student_id')->count();

        $averageScore = $tests->flatMap->results->avg('percentage') ?? 0;
        $passedResults = $tests->flatMap->results->where('is_passed', true)->count();
        $totalResults = $tests->flatMap->results->count();
        $averagePassRate = $totalResults > 0 ? ($passedResults / $totalResults) * 100 : 0;

        // Get all classes for filter
        $classes = ClassRoom::when($user->role === 2, function ($query) use ($user) {
            return $query->whereHas('timetables', function ($q) use ($user) {
                $q->where('teacher_id', $user->id);
            });
        })->get();

        return view('backend.pages.tests.reports', compact('tests', 'totalTests', 'totalStudents', 'averageScore', 'averagePassRate', 'classes'));
    }

    /**
     * Calculate average pass rate across all tests
     */
    private function calculateAveragePassRate($tests)
    {
        $testsWithResults = $tests->filter(function ($test) {
            return $test->results->isNotEmpty();
        });

        if ($testsWithResults->isEmpty()) {
            return 0;
        }

        $totalPassRate = $testsWithResults->sum(function ($test) {
            return ($test->results->where('is_passed', true)->count() / $test->results->count()) * 100;
        });

        return $totalPassRate / $testsWithResults->count();
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
