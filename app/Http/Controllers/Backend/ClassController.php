<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\ClassRoom;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ClassController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $classes = ClassRoom::with('teacher')->get();
        return view('backend.pages.classes.index', compact('classes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $teachers = User::where('role', 2)->get(); // Role 2 is for teachers
        return view('backend.pages.classes.create', compact('teachers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Debug: Log the incoming request data
        Log::info('Class creation request data:', $request->all());

        // Set is_active to true by default if not provided
        $data = $request->all();
        $data['is_active'] = $request->has('is_active') ? true : false;

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'grade_year' => 'required|string|max:255',
            'teacher_id' => 'nullable|exists:users,id',
            'capacity' => 'required|integer|min:1|max:100',
            'room_number' => 'nullable|string|max:50',
            'description' => 'nullable|string',
        ]);

        // Debug: Log the validated data
        Log::info('Class validated data:', $validated);

        $validated['is_active'] = $data['is_active'];

        // Debug: Log the final data before creation
        Log::info('Class final data for creation:', $validated);

        try {
            $class = ClassRoom::create($validated);

            // Debug: Log the created class
            Log::info('Class created successfully:', $class->toArray());

            return redirect()->route('classes.index')->with('success', 'Class created successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to create class: ' . $e->getMessage());
            Log::error('Exception trace: ' . $e->getTraceAsString());
            return back()->withInput()->with('error', 'Failed to create class. Please try again.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ClassRoom $class)
    {
        $class->load('teacher', 'students');
        return view('backend.pages.classes.show', compact('class'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ClassRoom $class)
    {
        $teachers = User::where('role', 2)->get(); // Role 2 is for teachers
        return view('backend.pages.classes.edit', compact('class', 'teachers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ClassRoom $class)
    {
        // Debug: Log the incoming request data
        Log::info('Class update request data:', $request->all());

        // Set is_active to true by default if not provided
        $data = $request->all();
        $data['is_active'] = $request->has('is_active') ? true : false;

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'grade_year' => 'required|string|max:255',
            'teacher_id' => 'nullable|exists:users,id',
            'capacity' => 'required|integer|min:1|max:100',
            'room_number' => 'nullable|string|max:50',
            'description' => 'nullable|string',
        ]);

        // Debug: Log the validated data
        Log::info('Class validated data:', $validated);

        $validated['is_active'] = $data['is_active'];

        // Debug: Log the final data before update
        Log::info('Class final data for update:', $validated);

        try {
            $class->update($validated);

            // Debug: Log the updated class
            Log::info('Class updated successfully:', $class->toArray());

            return redirect()->route('classes.index')->with('success', 'Class updated successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to update class: ' . $e->getMessage());
            Log::error('Exception trace: ' . $e->getTraceAsString());
            return back()->withInput()->with('error', 'Failed to update class. Please try again.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ClassRoom $class)
    {
        try {
            $class->delete();
            return redirect()->route('classes.index')->with('success', 'Class deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to delete class: ' . $e->getMessage());
            return back()->with('error', 'Failed to delete class. Please try again.');
        }
    }

    /**
     * Show the form for managing students in a class.
     */
    public function manageStudents(ClassRoom $class)
    {
        $class->load('students');
        $students = User::where('role', 4)->get(); // Role 4 is for students
        return view('backend.pages.classes.manage-students', compact('class', 'students'));
    }

    /**
     * Update the students in a class.
     */
    public function updateStudents(Request $request, ClassRoom $class)
    {
        $validated = $request->validate([
            'student_ids' => 'nullable|array',
            'student_ids.*' => 'exists:users,id',
        ]);

        try {
            $class->students()->sync($request->student_ids ?? []);
            return redirect()->route('classes.show', $class)->with('success', 'Students updated successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to update students: ' . $e->getMessage());
            return back()->with('error', 'Failed to update students. Please try again.');
        }
    }
}
