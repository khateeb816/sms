<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Datesheet;
use App\Models\Exam;
use App\Models\ClassRoom;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DatesheetController extends Controller
{
    /**
     * Display a listing of the datesheets.
     */
    public function index()
    {
        $user = Auth::user();
        $datesheets = [];

        if ($user->role === 1) { // Admin
            $datesheets = Datesheet::with(['class'])
                ->latest()
                ->get();
        } elseif ($user->role === 2) { // Teacher
            // Get classes assigned to teacher in timetable
            $teacherClassIds = DB::table('timetables')
                ->where('teacher_id', $user->id)
                ->distinct()
                ->pluck('class_id');

            $datesheets = Datesheet::whereIn('class_id', $teacherClassIds)
                ->with(['class'])
                ->latest()
                ->get();
        } elseif ($user->role === 3) { // Parent
            // Get all children of the parent
            $children = User::where('parent_id', $user->id)
                ->where('role', 4) // Student role
                ->get();

            // Get class IDs for each child from class_student table
            $classIds = [];
            foreach ($children as $child) {
                $childClassIds = DB::table('class_student')
                    ->where('student_id', $child->id)
                    ->pluck('class_id');
                $classIds = array_merge($classIds, $childClassIds->toArray());
            }
            $classIds = array_unique($classIds);

            // Get only published datesheets for these specific classes
            $datesheets = Datesheet::whereIn('class_id', $classIds)
                ->where('status', 'published')
                ->with(['class'])
                ->latest()
                ->get();

            // Add child information to each datesheet
            $datesheets->each(function($datesheet) use ($children) {
                $datesheet->children = $children->filter(function($child) use ($datesheet) {
                    return DB::table('class_student')
                        ->where('student_id', $child->id)
                        ->where('class_id', $datesheet->class_id)
                        ->exists();
                })->map(function($child) {
                    return [
                        'id' => $child->id,
                        'name' => $child->name,
                        'roll_number' => $child->roll_number
                    ];
                });
            });
        }

        return view('backend.pages.datesheets.index', compact('datesheets'));
    }

    /**
     * Show the form for creating a new datesheet.
     */
    public function create()
    {
        $user = Auth::user();

        if ($user->role === 1) { // Admin
            $classes = ClassRoom::all();
        } else { // Teacher
            $classes = ClassRoom::where('teacher_id', $user->id)->get();

            if ($classes->isEmpty()) {
                return redirect()->route('datesheets.index')
                    ->with('error', 'You need to be assigned to at least one class to create a datesheet.');
            }
        }

        return view('backend.pages.datesheets.create', compact('classes'));
    }

    /**
     * Store a newly created datesheet in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'class_id' => 'required|exists:class_rooms,id',
            'term' => 'required|in:first,second,third,final',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => [
                'required',
                'date',
                'after_or_equal:start_date'
            ],
            'description' => 'nullable|string',
            'instructions' => 'nullable|string'
        ]);

        $datesheet = Datesheet::create([
            'status' => 'draft',
            ...$request->all()
        ]);

        return redirect()->route('datesheets.show', $datesheet)
            ->with('success', 'Datesheet created successfully.');
    }

    /**
     * Display the specified datesheet.
     */
    public function show(Datesheet $datesheet)
    {
        $user = Auth::user();

        // Check if user has permission to view this datesheet
        if ($user->role === 2) { // Teacher
            if ($datesheet->class->teacher_id !== $user->id) {
                return redirect()->route('datesheets.index')
                    ->with('error', 'You do not have permission to view this datesheet.');
            }
        }

        $datesheet->load(['class', 'exams.teacher']);
        return view('backend.pages.datesheets.show', compact('datesheet'));
    }

    /**
     * Show the form for editing the specified datesheet.
     */
    public function edit(Datesheet $datesheet)
    {
        $user = Auth::user();

        if ($user->role === 1) { // Admin
            $classes = ClassRoom::all();
        } else { // Teacher
            $classes = ClassRoom::where('teacher_id', $user->id)->get();
        }

        $datesheet->load(['class', 'exams']);
        return view('backend.pages.datesheets.edit', compact('datesheet', 'classes'));
    }

    /**
     * Update the specified datesheet in storage.
     */
    public function update(Request $request, Datesheet $datesheet)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'class_id' => 'required|exists:class_rooms,id',
            'term' => 'required|in:first,second,third,final',
            'start_date' => 'required|date',
            'end_date' => [
                'required',
                'date',
                'after_or_equal:start_date'
            ],
            'status' => 'required|in:draft,published,completed',
            'description' => 'nullable|string',
            'instructions' => 'nullable|string'
        ]);

        $datesheet->update($request->all());

        return redirect()->route('datesheets.show', $datesheet)
            ->with('success', 'Datesheet updated successfully.');
    }

    /**
     * Remove the specified datesheet from storage.
     */
    public function destroy(Datesheet $datesheet)
    {
        $datesheet->delete();

        return redirect()->route('datesheets.index')
            ->with('success', 'Datesheet deleted successfully.');
    }

    /**
     * Show the form for managing exams in the datesheet.
     */
    public function manageExams(Datesheet $datesheet)
    {
        $datesheet->load(['class', 'exams']);

        // Get available exams for this class that are not in any datesheet
        $availableExams = Exam::where('class_id', $datesheet->class_id)
            ->whereDoesntHave('datesheets')
            ->where('status', 'scheduled')
            ->get();

        return view('backend.pages.datesheets.manage-exams', compact('datesheet', 'availableExams'));
    }

    /**
     * Update exams in the datesheet.
     */
    public function updateExams(Request $request, Datesheet $datesheet)
    {
        $request->validate([
            'exams' => 'required|array',
            'exams.*.exam_id' => 'required|exists:exams,id',
            'exams.*.day_number' => 'required|integer|min:1'
        ]);

        DB::transaction(function () use ($request, $datesheet) {
            // Remove all existing exam associations
            $datesheet->exams()->detach();

            // Add new exam associations with day numbers
            foreach ($request->exams as $examData) {
                $datesheet->exams()->attach($examData['exam_id'], [
                    'day_number' => $examData['day_number']
                ]);
            }
        });

        return redirect()->route('datesheets.show', $datesheet)
            ->with('success', 'Datesheet exams updated successfully.');
    }

    /**
     * Publish the datesheet.
     */
    public function publish(Datesheet $datesheet)
    {
        if ($datesheet->exams->isEmpty()) {
            return back()->with('error', 'Cannot publish datesheet without any exams.');
        }

        $datesheet->update(['status' => 'published']);

        return redirect()->route('datesheets.show', $datesheet)
            ->with('success', 'Datesheet published successfully.');
    }

    /**
     * Print the datesheet.
     */
    public function print(Datesheet $datesheet)
    {
        $datesheet->load(['class', 'exams.teacher']);
        return view('backend.pages.datesheets.print', compact('datesheet'));
    }

    /**
     * Publish exam results for the datesheet.
     */
    public function publishResults(Datesheet $datesheet)
    {
        // Check if the datesheet has any exam
        if ($datesheet->exams->isEmpty()) {
            return back()->with('error', 'Cannot publish results without any exams.');
        }

        // Check if all exams have completed status
        $incompleteExams = $datesheet->exams->where('status', '!=', 'completed')->count();
        if ($incompleteExams > 0) {
            return back()->with('error', 'Cannot publish results. Some exams are not completed yet.');
        }

        // Update the datesheet to publish results
        $datesheet->update(['is_result_published' => true]);

        return redirect()->route('datesheets.show', $datesheet)
            ->with('success', 'Exam results published successfully.');
    }

    /**
     * Unpublish exam results for the datesheet.
     */
    public function unpublishResults(Datesheet $datesheet)
    {
        // Update the datesheet to unpublish results
        $datesheet->update(['is_result_published' => false]);

        return redirect()->route('datesheets.show', $datesheet)
            ->with('success', 'Exam results unpublished successfully.');
    }
}
