<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Services\ActivityService;

class StudentController extends Controller
{
    /**
     * Display a listing of the students.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Get the authenticated user
        $user = auth()->user();

        // Query builder for students
        $studentsQuery = User::where('role', 4);

        if ($user->role == 2) { // Teacher role
            // Get classes where the teacher has timetable entries
            $teacherClassIds = DB::table('timetables')
                ->where('teacher_id', $user->id)
                ->distinct()
                ->pluck('class_id');

            // Get students from these classes
            $studentsQuery->whereHas('classes', function ($query) use ($teacherClassIds) {
                $query->whereIn('class_rooms.id', $teacherClassIds);
            });
        }

        $students = $studentsQuery->get();

        // Get class information for each student from class_student table
        foreach ($students as $student) {
            $student->class = DB::table('class_student')
                ->join('class_rooms', 'class_student.class_id', '=', 'class_rooms.id')
                ->where('class_student.student_id', $student->id)
                ->select('class_rooms.name')
                ->first();
        }

        return view('backend.pages.students.index', compact('students'));
    }

    /**
     * Show the form for creating a new student.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Get the authenticated user
        $user = auth()->user();

        // If user is a teacher, prevent access
        if ($user->role == 2) {
            return redirect()->route('students.index')
                ->with('error', 'Teachers are not allowed to create new students.');
        }

        // Get all parents for the dropdown
        $parents = User::where('role', 3)->where('status', 'active')->get();

        return view('backend.pages.students.create', compact('parents'));
    }

    /**
     * Store a newly created student in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Get the authenticated user
        $user = auth()->user();

        // If user is a teacher, prevent creation
        if ($user->role == 2) {
            return redirect()->route('students.index')
                ->with('error', 'Teachers are not allowed to create new students.');
        }

        // Validate the request
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'class' => 'nullable|string|max:50',
            'roll_number' => 'nullable|string|max:20',
            'parent_id' => 'nullable|exists:users,id',
            'status' => 'required|in:active,inactive',
        ]);

        // Create a new user with role=4 (student)
        $user = new User();
        $user->name = $request->name;
        // Generate a unique email based on name and timestamp
        $user->email = strtolower(str_replace(' ', '.', $request->name)) . time() . '@school.com';
        $user->phone = $request->phone;
        $user->password = Hash::make('123456'); // Default password for all students
        $user->address = $request->address;
        $user->class = $request->class;
        $user->roll_number = $request->roll_number;
        $user->parent_id = $request->parent_id;
        $user->status = $request->status;
        $user->role = 4; // 4 = student
        $user->save();

        // Log the activity
        ActivityService::logStudentActivity('Created', $user->name, $user->id);

        return redirect('/dash/students')->with('success', 'Student added successfully!');
    }

    /**
     * Display the specified student.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // Get the authenticated user
        $user = auth()->user();

        // Find the student user
        $student = User::where('role', 4)->findOrFail($id);

        // If user is a teacher, verify they have access to this student
        if ($user->role == 2) {
            $teacherClassIds = DB::table('timetables')
                ->where('teacher_id', $user->id)
                ->distinct()
                ->pluck('class_id');

            $hasAccess = $student->classes()
                ->whereIn('class_rooms.id', $teacherClassIds)
                ->exists();

            if (!$hasAccess) {
                return redirect()->route('students.index')
                    ->with('error', 'You do not have permission to view this student.');
            }
        }

        // Get parent information if available
        $parent = null;
        if ($student->parent_id) {
            $parent = User::find($student->parent_id);
        }

        return view('backend.pages.students.show', compact('student', 'parent'));
    }

    /**
     * Show the form for editing the specified student.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // Get the authenticated user
        $user = auth()->user();

        // Find the student user
        $student = User::where('role', 4)->findOrFail($id);

        // If user is a teacher, verify they have access to this student
        if ($user->role == 2) {
            return redirect()->route('students.index')
                ->with('error', 'Teachers are not allowed to edit student information.');
        }

        // Get all parents for the dropdown
        $parents = User::where('role', 3)->where('status', 'active')->get();

        return view('backend.pages.students.edit', compact('student', 'parents'));
    }

    /**
     * Update the specified student in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Get the authenticated user
        $user = auth()->user();

        // If user is a teacher, prevent update
        if ($user->role == 2) {
            return redirect()->route('students.index')
                ->with('error', 'Teachers are not allowed to modify student information.');
        }

        // Find the student user
        $student = User::where('role', 4)->findOrFail($id);

        // Validate the request
        $rules = [
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'class' => 'nullable|string|max:50',
            'roll_number' => 'nullable|string|max:20',
            'parent_id' => 'nullable|exists:users,id',
            'status' => 'required|in:active,inactive',
        ];

        $request->validate($rules);

        // Update the student
        $student->name = $request->name;
        $student->phone = $request->phone;
        $student->address = $request->address;
        $student->class = $request->class;
        $student->roll_number = $request->roll_number;
        $student->parent_id = $request->parent_id;
        $student->status = $request->status;

        $student->save();

        // Log the activity
        ActivityService::logStudentActivity('Updated', $student->name, $student->id);

        return redirect('/dash/students')->with('success', 'Student updated successfully!');
    }

    /**
     * Remove the specified student from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // Get the authenticated user
        $user = auth()->user();

        // If user is a teacher, prevent deletion
        if ($user->role == 2) {
            return redirect()->route('students.index')
                ->with('error', 'Teachers are not allowed to delete students.');
        }

        // Find the student user
        $student = User::where('role', 4)->findOrFail($id);

        $studentName = $student->name;
        $student->delete();

        // Log the activity
        ActivityService::logStudentActivity('Deleted', $studentName, $id);

        return redirect('/dash/students')->with('success', "Student '{$studentName}' deleted successfully!");
    }

    /**
     * Update the student's profile picture.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateProfilePicture(Request $request, $id)
    {
        // Find the student user
        $student = User::where('role', 4)->findOrFail($id);

        // Validate the uploaded file
        $request->validate([
            'profile_picture' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle file upload
        if ($request->hasFile('profile_picture')) {
            // Delete old profile picture if exists
            if ($student->image && file_exists(public_path('storage/profile_pictures/' . $student->image))) {
                unlink(public_path('storage/profile_pictures/' . $student->image));
            }

            // Get filename with extension
            $filenameWithExt = $request->file('profile_picture')->getClientOriginalName();
            // Get just filename
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            // Get just extension
            $extension = $request->file('profile_picture')->getClientOriginalExtension();
            // Filename to store
            $fileNameToStore = $filename . '_' . time() . '.' . $extension;

            // Upload image
            $path = $request->file('profile_picture')->storeAs('public/profile_pictures', $fileNameToStore);

            // Update database
            $student->image = $fileNameToStore;
            $student->save();
        }

        return redirect('/dash/students/' . $id)->with('success', 'Profile picture updated successfully!');
    }
}
