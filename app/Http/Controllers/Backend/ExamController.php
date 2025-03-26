<?php

namespace App\Http\Controllers\Backend;

use App\Models\Exam;
use App\Models\User;
use App\Models\ClassRoom;
use App\Models\Datesheet;
use App\Models\ExamResult;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\ActivityService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

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
        $teachers = $user->role === 1 ? User::where('role', 2)->get() : null; // Get teachers only for admin

        return view('backend.pages.exams.create', compact('classes', 'teachers'));
    }

    /**
     * Store a newly created exam in storage.
     */
    public function store(Request $request)
    {
        $validationRules = [
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
        ];

        // Add teacher_id validation for admin
        if (Auth::user()->role === 1) {
            $validationRules['teacher_id'] = 'required|exists:users,id';
        }

        $request->validate($validationRules);

        // Set teacher_id based on user role
        $teacherId = Auth::user()->role === 1 ? $request->teacher_id : Auth::id();

        $exam = Exam::create([
            'teacher_id' => $teacherId,
            'status' => Exam::STATUS_SCHEDULED,
            ...$request->except(['teacher_id']), // Exclude teacher_id to prevent duplicate field error
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

        $user = Auth::user();
        $classes = $user->role === 1 ? ClassRoom::all() : ClassRoom::where('teacher_id', Auth::id())->get();
        $teachers = $user->role === 1 ? User::where('role', 2)->get() : null; // Get teachers only for admin

        // Format the time values for the form
        $exam->start_time = date('H:i', strtotime($exam->start_time));
        $exam->end_time = date('H:i', strtotime($exam->end_time));

        return view('backend.pages.exams.edit', compact('exam', 'classes', 'teachers'));
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

        $validationRules = [
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
        ];

        // Add teacher_id validation for admin
        if (Auth::user()->role === 1) {
            $validationRules['teacher_id'] = 'required|exists:users,id';
        }

        $request->validate($validationRules);

        $dataToUpdate = $request->all();

        // Only admin can update teacher_id
        if (Auth::user()->role !== 1) {
            unset($dataToUpdate['teacher_id']);
        }

        $exam->update($dataToUpdate);
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

    /**
     * Display exam results for a parent's children.
     */
    public function examResults(Request $request)
    {
        $user = Auth::user();
        $startTime = microtime(true);

        // Initialize variables
        $classFilter = $request->input('class_id');
        $studentFilter = $request->input('student_id');
        $termFilter = $request->input('term');
        $dateFilter = $request->input('date');
        $subjectFilter = $request->input('subject');
        $perPage = $request->input('per_page', 10); // Default to 10 items per page

        // Generate a cache key based on user and filters
        $cacheKey = sprintf(
            'exam_results_%d_%s_%s_%s_%s_%s_%s_%s',
            $user->id,
            $classFilter ?? 'all',
            $studentFilter ?? 'all',
            $termFilter ?? 'all',
            $dateFilter ?? 'all',
            $subjectFilter ?? 'all',
            $perPage,
            $request->input('page', 1)
        );

        // Try to get from cache first (cache for 30 minutes)
        if (!$request->has('nocache') && Cache::has($cacheKey)) {
            $cachedData = Cache::get($cacheKey);
            $cachedData['execution_time'] = microtime(true) - $startTime;
            $cachedData['from_cache'] = true;
            return view('backend.pages.exams.exam-results', $cachedData);
        }

        // Get available filters - only select necessary columns
        $classes = collect([]);
        $students = collect([]);
        $terms = ['first' => 'First Term', 'second' => 'Second Term', 'third' => 'Third Term', 'final' => 'Final Term'];
        $subjects = collect([]);

        // Build basic query for datesheets without eager loading yet
        $datesheetQuery = Datesheet::select('id', 'title', 'class_id', 'term', 'start_date', 'end_date', 'description', 'instructions')
            ->where('status', 'published')
            ->where('is_result_published', true);

        // Apply role-based restrictions and get student IDs
        $studentIds = $this->getStudentIdsBasedOnRole($user, $classFilter, $datesheetQuery, $classes, $students, $subjects);

        // Apply additional filters
        if ($termFilter && $termFilter != '') {
            $datesheetQuery->where('term', $termFilter);
        }

        if ($dateFilter) {
            $datesheetQuery->whereDate('start_date', '<=', $dateFilter)
                ->whereDate('end_date', '>=', $dateFilter);
        }

        // Debug term filter
        Log::info('Term filter value:', [
            'termFilter' => $termFilter,
            'type' => gettype($termFilter),
            'isEmpty' => empty($termFilter),
            'isBlank' => $termFilter === '',
            'query' => $datesheetQuery->toSql(),
            'bindings' => $datesheetQuery->getBindings()
        ]);

        // Apply additional filters
        if ($termFilter && $termFilter != '') {
            $datesheetQuery->where('term', $termFilter);
            Log::info('Term filter applied', [
                'SQL' => $datesheetQuery->toSql(),
                'bindings' => $datesheetQuery->getBindings()
            ]);
        }

        // Get all datasheets first before pagination for proper filtering
        $datesheetIds = $datesheetQuery->pluck('id')->toArray();

        // Get exam IDs for these datesheets
        $examIds = DB::table('datesheet_exam')
            ->whereIn('datesheet_id', $datesheetIds)
            ->pluck('exam_id')
            ->toArray();

        // Get exams with filtered conditions
        $exams = Exam::whereIn('id', $examIds)
            ->where('status', Exam::STATUS_COMPLETED)
            ->when($subjectFilter && $subjectFilter != '', function($query) use ($subjectFilter) {
                return $query->where('subject', $subjectFilter);
            })
            ->select('id', 'teacher_id', 'class_id', 'subject', 'total_marks', 'type')
            ->get();

        $filteredExamIds = $exams->pluck('id')->toArray();

        // Get the datesheet IDs that have the filtered exams
        $filteredDatesheetIds = DB::table('datesheet_exam')
            ->whereIn('exam_id', $filteredExamIds)
            ->pluck('datesheet_id')
            ->unique()
            ->toArray();

        // Reapply the query with the filtered datesheet IDs to ensure pagination works correctly
        $datesheetQuery = $datesheetQuery->whereIn('id', $filteredDatesheetIds);

        // Now paginate the filtered datesheets
        $datesheets = $datesheetQuery->paginate($perPage);

        // Now load the relationships for just this page of datesheets
        $datesheetIds = $datesheets->pluck('id')->toArray();

        // Efficient eager loading only for the current page
        if (!empty($datesheetIds)) {
            // First, load the classes for the datesheets
            $classes = ClassRoom::whereIn('id', Datesheet::whereIn('id', $datesheetIds)->pluck('class_id'))
                ->select('id', 'name')
                ->get()
                ->keyBy('id');

            // Attach classes to datesheets manually
            foreach ($datesheets as $datesheet) {
                $datesheet->class = $classes[$datesheet->class_id] ?? null;
            }

            // Get results for these exams with student restriction if needed
            $results = ExamResult::whereIn('exam_id', $exams->pluck('id'))
                ->when($studentFilter && $studentFilter != '', function($query) use ($studentFilter) {
                    return $query->where('student_id', $studentFilter);
                })
                ->select('id', 'exam_id', 'student_id', 'marks_obtained', 'percentage', 'grade', 'is_passed')
                ->get();

            // If no results found due to student filter, we should return empty data
            if ($results->isEmpty() && $studentFilter && $studentFilter != '') {
                $datesheets = collect([]);
            }

            // Get unique student IDs from results
            $resultStudentIds = $results->pluck('student_id')->unique()->toArray();

            // Get student details for the results
            $resultStudents = User::whereIn('id', $resultStudentIds)
                ->select('id', 'name')
                ->get()
                ->keyBy('id');

            // Manually set up relationships
            $examsById = $exams->keyBy('id');
            $resultsByExamId = $results->groupBy('exam_id');

            // Attach student data to results
            foreach ($results as $result) {
                $result->student = $resultStudents[$result->student_id] ?? null;
            }

            // Need to build the relationship between datesheets and exams manually
            // since we're not using eager loading
            $datesheetExamMapping = DB::table('datesheet_exam')
                ->whereIn('datesheet_id', $datesheetIds)
                ->whereIn('exam_id', $exams->pluck('id'))
                ->get(['datesheet_id', 'exam_id']);

            // Create a lookup by datesheet ID
            $examsByDatesheet = [];
            foreach ($datesheetExamMapping as $mapping) {
                if (!isset($examsByDatesheet[$mapping->datesheet_id])) {
                    $examsByDatesheet[$mapping->datesheet_id] = collect([]);
                }
                $exam = $exams->firstWhere('id', $mapping->exam_id);
                if ($exam) {
                    $examsByDatesheet[$mapping->datesheet_id]->push($exam);
                }
            }

            // Attach exams to datesheets
            foreach ($datesheets as $datesheet) {
                $datesheet->exams = $examsByDatesheet[$datesheet->id] ?? collect([]);

                // Attach results to exams
                foreach ($datesheet->exams as $exam) {
                    $exam->results = $resultsByExamId[$exam->id] ?? collect([]);
                }

                // Filter exams with no results
                $datesheet->exams = $datesheet->exams->filter(function($exam) {
                    return $exam->results->isNotEmpty();
                });
            }

            // Filter datesheets with no exams or results
            $datesheets = $datesheets->filter(function($datesheet) {
                return $datesheet->exams->isNotEmpty();
            });
        }

        // Group results by student (optimized)
        $groupedResults = $this->groupResultsByStudentOptimizedFast($datesheets, $studentIds);

        $filters = (object) [
            'class_id' => $classFilter,
            'student_id' => $studentFilter,
            'term' => $termFilter,
            'date' => $dateFilter,
            'subject' => $subjectFilter
        ];

        $viewData = compact(
            'datesheets',
            'groupedResults',
            'classes',
            'students',
            'terms',
            'subjects',
            'filters'
        );

        // Add execution time for debugging
        $viewData['execution_time'] = microtime(true) - $startTime;
        $viewData['from_cache'] = false;

        // Cache the results for 30 minutes
        Cache::put($cacheKey, $viewData, now()->addMinutes(30));

        return view('backend.pages.exams.exam-results', $viewData);
    }

    /**
     * Get student IDs based on user role and apply role-based restrictions to query
     */
    private function getStudentIdsBasedOnRole($user, $classFilter, &$datesheetQuery, &$classes, &$students, &$subjects)
    {
        $studentIds = [];

        if ($user->role === 1) { // Admin
            $classes = ClassRoom::select('id', 'name')->get();

            if ($classFilter) {
                $datesheetQuery->where('class_id', $classFilter);
                $students = User::where('role', 4)
                    ->whereHas('classes', function($query) use ($classFilter) {
                        $query->where('class_rooms.id', $classFilter);
                    })
                    ->select('id', 'name')
                    ->get();
                $studentIds = $students->pluck('id')->toArray();
            } else {
                $students = User::where('role', 4)
                    ->select('id', 'name')
                    ->limit(100) // Limit to avoid performance issues
                    ->get();
                $studentIds = $students->pluck('id')->toArray();
            }

            $subjects = Exam::select('subject')
                ->distinct()
                ->limit(30) // Limit to avoid performance issues
                ->pluck('subject');
        }
        elseif ($user->role === 2) { // Teacher
            $classIds = ClassRoom::where('teacher_id', $user->id)
                ->pluck('id')
                ->toArray();

            $examClassIds = Exam::where('teacher_id', $user->id)
                ->distinct()
                ->pluck('class_id')
                ->toArray();

            $classIds = array_unique(array_merge($classIds, $examClassIds));
            $classes = ClassRoom::whereIn('id', $classIds)
                ->select('id', 'name')
                ->get();

            $datesheetQuery->whereIn('class_id', $classIds);

            if ($classFilter && in_array($classFilter, $classIds)) {
                $datesheetQuery->where('class_id', $classFilter);
                $students = User::where('role', 4)
                    ->whereHas('classes', function($query) use ($classFilter) {
                        $query->where('class_rooms.id', $classFilter);
                    })
                    ->select('id', 'name')
                    ->get();
                $studentIds = $students->pluck('id')->toArray();
            } else {
                $students = User::where('role', 4)
                    ->whereHas('classes', function($query) use ($classIds) {
                        $query->whereIn('class_rooms.id', $classIds);
                    })
                    ->select('id', 'name')
                    ->get();
                $studentIds = $students->pluck('id')->toArray();
            }

            $subjects = Exam::whereIn('class_id', $classIds)
                ->select('subject')
                ->distinct()
                ->pluck('subject');
        }
        elseif ($user->role === 3) { // Parent
            $children = User::where('parent_id', $user->id)
                ->select('id', 'name')
                ->get();

            if ($children->isEmpty()) {
                return [];
            }

            $students = $children;
            $childrenIds = $children->pluck('id')->toArray();

            $classIds = DB::table('class_student')
                ->whereIn('student_id', $childrenIds)
                ->pluck('class_id')
                ->unique()
                ->toArray();

            $classes = ClassRoom::whereIn('id', $classIds)
                ->select('id', 'name')
                ->get();

            $datesheetQuery->whereIn('class_id', $classIds);

            if ($classFilter) {
                $datesheetQuery->where('class_id', $classFilter);
            }

            $studentIds = $childrenIds;
            $subjects = Exam::whereIn('class_id', $classIds)
                ->select('subject')
                ->distinct()
                ->pluck('subject');
        }
        else { // Student
            $classIds = DB::table('class_student')
                ->where('student_id', $user->id)
                ->pluck('class_id')
                ->toArray();

            $classes = ClassRoom::whereIn('id', $classIds)
                ->select('id', 'name')
                ->get();

            $datesheetQuery->whereIn('class_id', $classIds);

            if ($classFilter) {
                $datesheetQuery->where('class_id', $classFilter);
            }

            $studentIds = [$user->id];
            $students = collect([$user]);
            $subjects = Exam::whereIn('class_id', $classIds)
                ->select('subject')
                ->distinct()
                ->pluck('subject');
        }

        return $studentIds;
    }

    /**
     * Faster optimized method to group results by student
     */
    private function groupResultsByStudentOptimizedFast($datesheets, $studentIds)
    {
        $groupedResults = collect();
        $students = User::whereIn('id', $studentIds)
            ->select('id', 'name')
            ->get()
            ->keyBy('id');

        foreach ($students as $studentId => $student) {
            $studentResults = collect();

            foreach ($datesheets as $datesheet) {
                $subjects = collect();

                foreach ($datesheet->exams as $exam) {
                    $result = $exam->results->where('student_id', $studentId)->first();
                    if ($result) {
                        $subjects->push([
                            'exam' => $exam,
                            'result' => $result,
                            'subject' => $exam->subject
                        ]);
                    }
                }

                if ($subjects->isNotEmpty()) {
                    $studentResults->put($datesheet->id, [
                        'datesheet' => $datesheet,
                        'subjects' => $subjects
                    ]);
                }
            }

            if ($studentResults->isNotEmpty()) {
                $groupedResults->put($student->name, $studentResults);
            }
        }

        return $groupedResults;
    }

    /**
     * Display exam results for a teacher's students with filters.
     */

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

    /**
     * Generate PDF for a single datesheet's results
     */
    public function downloadResultPdf($datesheetId)
    {
        try {
            $user = Auth::user();

            // Get the datesheet using Eloquent model with eager loading
            $datesheet = Datesheet::with([
                'class',
                'exams' => function($query) {
                    $query->where('status', Exam::STATUS_COMPLETED);
                },
                'exams.teacher',
                'exams.class',
                'exams.results.student'
            ])->findOrFail($datesheetId);

            // Check if the datesheet is published and has published results
            if ($datesheet->status !== 'published' || !$datesheet->is_result_published) {
                return redirect()->back()->with('error', 'Result not found or not published yet.');
            }

            // Check if user has access to this datesheet
            if ($user->role !== 1) {
                if ($user->role === 2) { // Teacher
                    $classIds = ClassRoom::where('teacher_id', $user->id)
                        ->pluck('id')
                        ->toArray();

                    $examClassIds = Exam::where('teacher_id', $user->id)
                        ->distinct()
                        ->pluck('class_id')
                        ->toArray();

                    $classIds = array_unique(array_merge($classIds, $examClassIds));

                    if (!in_array($datesheet->class_id, $classIds)) {
                        return redirect()->back()->with('error', 'You do not have access to this result.');
                    }
                } elseif ($user->role === 3) { // Parent
                    $childrenIds = User::where('parent_id', $user->id)->pluck('id')->toArray();
                    $classIds = DB::table('class_student')
                        ->whereIn('student_id', $childrenIds)
                        ->pluck('class_id')
                        ->unique()
                        ->toArray();

                    if (!in_array($datesheet->class_id, $classIds)) {
                        return redirect()->back()->with('error', 'You do not have access to this result.');
                    }
                } elseif ($user->role === 4) { // Student
                    $classIds = DB::table('class_student')
                        ->where('student_id', $user->id)
                        ->pluck('class_id')
                        ->toArray();

                    if (!in_array($datesheet->class_id, $classIds)) {
                        return redirect()->back()->with('error', 'You do not have access to this result.');
                    }
                }
            }

            // Filter exams with no results
            $datesheet->exams = $datesheet->exams->filter(function($exam) {
                return $exam->results->isNotEmpty();
            });

            if ($datesheet->exams->isEmpty()) {
                return redirect()->back()->with('error', 'No exams with results found for this datesheet.');
            }

            // Get student IDs based on user role
            $studentIds = [];
            if ($user->role === 1 || $user->role === 2) {
                $studentIds = User::where('role', 4)
                    ->whereHas('classes', function($query) use ($datesheet) {
                        $query->where('class_rooms.id', $datesheet->class_id);
                    })
                    ->pluck('id')
                    ->toArray();
            } elseif ($user->role === 3) {
                $studentIds = User::where('parent_id', $user->id)->pluck('id')->toArray();
            } elseif ($user->role === 4) {
                $studentIds = [$user->id];
            }

            // Group results by student using the optimized method
            $groupedResults = $this->groupResultsByStudentOptimized(collect([$datesheet]), $studentIds);

            if ($groupedResults->isEmpty()) {
                return redirect()->back()->with('error', 'No results found for any students.');
            }

            // Get class name
            $className = $datesheet->class->name ?? '';

            // Generate PDF
            $pdf = Pdf::loadView('backend.pages.exams.pdf-result', [
                'datesheet' => $datesheet,
                'groupedResults' => $groupedResults,
                'className' => $className,
                'termName' => ucfirst($datesheet->term)
            ]);

            return $pdf->download('Exam_Result_' . str_replace(' ', '_', $datesheet->title) . '.pdf');
        } catch (\Exception $e) {
            // Log the error
            Log::error('PDF download error: ' . $e->getMessage() . ' ' . $e->getTraceAsString());
            return redirect()->back()->with('error', 'An error occurred while generating the PDF. Please try again later.');
        }
    }

    /**
     * Optimized method to group results by student for PDFs
     */
    private function groupResultsByStudentOptimized($datesheets, $studentIds)
    {
        $groupedResults = collect();
        $students = User::whereIn('id', $studentIds)
            ->select('id', 'name')
            ->get()
            ->keyBy('id');

        foreach ($students as $studentId => $student) {
            $studentResults = collect();

            foreach ($datesheets as $datesheet) {
                $subjects = collect();

                foreach ($datesheet->exams as $exam) {
                    $result = $exam->results->where('student_id', $studentId)->first();
                    if ($result) {
                        $subjects->push([
                            'exam' => $exam,
                            'result' => $result,
                            'subject' => $exam->subject
                        ]);
                    }
                }

                if ($subjects->isNotEmpty()) {
                    $studentResults->put($datesheet->id, [
                        'datesheet' => $datesheet,
                        'subjects' => $subjects
                    ]);
                }
            }

            if ($studentResults->isNotEmpty()) {
                $groupedResults->put($student->name, $studentResults);
            }
        }

        return $groupedResults;
    }

    /**
     * Generate PDF for all datesheets' results
     */
    public function downloadAllResultsPdf(Request $request)
    {
        $user = Auth::user();

        // Initialize variables
        $classFilter = $request->input('class_id');
        $studentFilter = $request->input('student_id');
        $termFilter = $request->input('term');
        $dateFilter = $request->input('date');
        $subjectFilter = $request->input('subject');

        // Get datesheets with filters and eager loading
        $datesheetQuery = Datesheet::with([
            'class',
            'exams' => function($query) use ($subjectFilter) {
                $query->where('status', Exam::STATUS_COMPLETED);
                if ($subjectFilter && $subjectFilter != '') {
                    $query->where('subject', $subjectFilter);
                }
            },
            'exams.teacher',
            'exams.class',
            'exams.results' => function($query) use ($studentFilter) {
                if ($studentFilter && $studentFilter != '') {
                    $query->where('student_id', $studentFilter);
                }
            },
            'exams.results.student'
        ])
        ->where('status', 'published')
        ->where('is_result_published', true);

        // Apply role-based restrictions
        if ($user->role === 2) { // Teacher
            $classIds = ClassRoom::where('teacher_id', $user->id)
                ->pluck('id')
                ->toArray();

            $examClassIds = Exam::where('teacher_id', $user->id)
                ->distinct()
                ->pluck('class_id')
                ->toArray();

            $classIds = array_unique(array_merge($classIds, $examClassIds));
            $datesheetQuery->whereIn('class_id', $classIds);
        } elseif ($user->role === 3) { // Parent
            $childrenIds = User::where('parent_id', $user->id)->pluck('id')->toArray();
            $classIds = DB::table('class_student')
                ->whereIn('student_id', $childrenIds)
                ->pluck('class_id')
                ->unique()
                ->toArray();

            $datesheetQuery->whereIn('class_id', $classIds);
        } elseif ($user->role === 4) { // Student
            $classIds = DB::table('class_student')
                ->where('student_id', $user->id)
                ->pluck('class_id')
                ->toArray();

            $datesheetQuery->whereIn('class_id', $classIds);
        }

        // Apply filters
        if ($classFilter) {
            $datesheetQuery->where('class_id', $classFilter);
        }

        if ($termFilter && $termFilter != '') {
            $datesheetQuery->where('term', $termFilter);
        }

        if ($dateFilter) {
            $datesheetQuery->whereDate('start_date', '<=', $dateFilter)
                ->whereDate('end_date', '>=', $dateFilter);
        }

        $datesheets = $datesheetQuery->get();

        // Filter datesheets with no exams or results
        $datesheets = $datesheets->map(function($datesheet) use ($studentFilter) {
            $datesheet->exams = $datesheet->exams->filter(function($exam) {
                return $exam->results->isNotEmpty();
            });
            return $datesheet;
        })->filter(function($datesheet) {
            return $datesheet->exams->isNotEmpty();
        });

        if ($datesheets->isEmpty()) {
            return redirect()->back()->with('error', 'No results found with the specified filters.');
        }

        // Get student IDs based on user role
        $studentIds = [];
        if ($user->role === 1) {
            if ($classFilter) {
                $studentIds = User::where('role', 4)
                    ->whereHas('classes', function($query) use ($classFilter) {
                        $query->where('class_rooms.id', $classFilter);
                    })
                    ->pluck('id')
                    ->toArray();
            } else {
                $studentIds = User::where('role', 4)->pluck('id')->toArray();
            }
        } elseif ($user->role === 2) {
            if ($classFilter) {
                $studentIds = User::where('role', 4)
                    ->whereHas('classes', function($query) use ($classFilter) {
                        $query->where('class_rooms.id', $classFilter);
                    })
                    ->pluck('id')
                    ->toArray();
            } else {
                $studentIds = User::where('role', 4)
                    ->whereHas('classes', function($query) use ($classIds) {
                        $query->whereIn('class_rooms.id', $classIds);
                    })
                    ->pluck('id')
                    ->toArray();
            }
        } elseif ($user->role === 3) {
            $studentIds = User::where('parent_id', $user->id)->pluck('id')->toArray();
        } elseif ($user->role === 4) {
            $studentIds = [$user->id];
        }

        // Apply student filter
        if ($studentFilter && $studentFilter != '') {
            $studentIds = [$studentFilter];
        }

        // Group results by student using optimized method
        $groupedResults = $this->groupResultsByStudentOptimized($datesheets, $studentIds);

        // Generate PDF
        $pdf = Pdf::loadView('backend.pages.exams.pdf-all-results', [
            'datesheets' => $datesheets,
            'groupedResults' => $groupedResults
        ]);

        return $pdf->download('All_Exam_Results.pdf');
    }
}
