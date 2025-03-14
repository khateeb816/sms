<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\TeacherDetail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class TeacherController extends Controller
{
    /**
     * Display a listing of the teachers.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Get all teachers (users with role=2)
        $teachers = User::where('role', 2)->with('teacherDetail')->get();
        return view('backend.pages.teachers.index', compact('teachers'));
    }

    /**
     * Show the form for creating a new teacher.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('backend.pages.teachers.create');
    }

    /**
     * Store a newly created teacher in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validate the request
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:6|confirmed',
            'address' => 'nullable|string',
            'qualification' => 'nullable|string|max:255',
            'specialization' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive',
            // Add validation for teacher details fields
            'education_level' => 'nullable|string|max:255',
            'university' => 'nullable|string|max:255',
            'degree' => 'nullable|string|max:255',
            'major' => 'nullable|string|max:255',
            'graduation_year' => 'nullable|integer|min:1900|max:' . (date('Y') + 10),
            'certification' => 'nullable|string|max:255',
            'teaching_experience' => 'nullable|string',
            'biography' => 'nullable|string',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'emergency_contact_relationship' => 'nullable|string|max:255',
            'bank_name' => 'nullable|string|max:255',
            'bank_account_number' => 'nullable|string|max:255',
            'bank_branch' => 'nullable|string|max:255',
        ]);

        try {
            // Create a new user with role=2 (teacher)
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->password = Hash::make($request->password);
            $user->address = $request->address;
            $user->status = $request->status;
            $user->role = 2; // 2 = teacher
            $user->save();

            // Create teacher details
            $teacherDetail = new TeacherDetail();
            $teacherDetail->user_id = $user->id;
            $teacherDetail->qualification = $request->qualification;
            $teacherDetail->specialization = $request->specialization;
            $teacherDetail->education_level = $request->education_level;
            $teacherDetail->university = $request->university;
            $teacherDetail->degree = $request->degree;
            $teacherDetail->major = $request->major;
            $teacherDetail->graduation_year = $request->graduation_year;
            $teacherDetail->certification = $request->certification;
            $teacherDetail->teaching_experience = $request->teaching_experience;
            $teacherDetail->biography = $request->biography;
            $teacherDetail->emergency_contact_name = $request->emergency_contact_name;
            $teacherDetail->emergency_contact_phone = $request->emergency_contact_phone;
            $teacherDetail->emergency_contact_relationship = $request->emergency_contact_relationship;
            $teacherDetail->bank_name = $request->bank_name;
            $teacherDetail->bank_account_number = $request->bank_account_number;
            $teacherDetail->bank_branch = $request->bank_branch;
            $teacherDetail->save();

            return redirect()->route('teachers.index')->with('success', 'Teacher added successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to add teacher: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified teacher.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            // Find the teacher user with their details
            $teacher = User::where('role', 2)->with('teacherDetail')->findOrFail($id);
            return view('backend.pages.teachers.show', compact('teacher'));
        } catch (\Exception $e) {
            return redirect()->route('teachers.index')->with('error', 'Teacher not found!');
        }
    }

    /**
     * Show the form for editing the specified teacher.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try {
            // Find the teacher user with their details
            $teacher = User::where('role', 2)->with('teacherDetail')->findOrFail($id);
            return view('backend.pages.teachers.edit', compact('teacher'));
        } catch (\Exception $e) {
            return redirect()->route('teachers.index')->with('error', 'Teacher not found!');
        }
    }

    /**
     * Update the specified teacher in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            // Find the teacher user
            $teacher = User::where('role', 2)->findOrFail($id);

            // Validate the request
            $rules = [
                'name' => 'required|string|max:255',
                'email' => [
                    'required',
                    'string',
                    'email',
                    'max:255',
                    Rule::unique('users')->ignore($teacher->id),
                ],
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string',
                'qualification' => 'nullable|string|max:255',
                'specialization' => 'nullable|string|max:255',
                'status' => 'required|in:active,inactive',
                // Add validation for teacher details fields
                'education_level' => 'nullable|string|max:255',
                'university' => 'nullable|string|max:255',
                'degree' => 'nullable|string|max:255',
                'major' => 'nullable|string|max:255',
                'graduation_year' => 'nullable|integer|min:1900|max:' . (date('Y') + 10),
                'certification' => 'nullable|string|max:255',
                'teaching_experience' => 'nullable|string',
                'biography' => 'nullable|string',
                'emergency_contact_name' => 'nullable|string|max:255',
                'emergency_contact_phone' => 'nullable|string|max:20',
                'emergency_contact_relationship' => 'nullable|string|max:255',
                'bank_name' => 'nullable|string|max:255',
                'bank_account_number' => 'nullable|string|max:255',
                'bank_branch' => 'nullable|string|max:255',
            ];

            // Add password validation if password is provided
            if ($request->filled('password')) {
                $rules['password'] = 'required|string|min:6|confirmed';
            }

            $request->validate($rules);

            // Update the teacher
            $teacher->name = $request->name;
            $teacher->email = $request->email;
            $teacher->phone = $request->phone;
            $teacher->address = $request->address;
            $teacher->status = $request->status;

            // Update password if provided
            if ($request->filled('password')) {
                $teacher->password = Hash::make($request->password);
            }

            $teacher->save();

            // Update or create teacher details
            $teacherDetail = TeacherDetail::updateOrCreate(
                ['user_id' => $teacher->id],
                [
                    'qualification' => $request->qualification,
                    'specialization' => $request->specialization,
                    'education_level' => $request->education_level,
                    'university' => $request->university,
                    'degree' => $request->degree,
                    'major' => $request->major,
                    'graduation_year' => $request->graduation_year,
                    'certification' => $request->certification,
                    'teaching_experience' => $request->teaching_experience,
                    'biography' => $request->biography,
                    'emergency_contact_name' => $request->emergency_contact_name,
                    'emergency_contact_phone' => $request->emergency_contact_phone,
                    'emergency_contact_relationship' => $request->emergency_contact_relationship,
                    'bank_name' => $request->bank_name,
                    'bank_account_number' => $request->bank_account_number,
                    'bank_branch' => $request->bank_branch,
                ]
            );

            return redirect()->route('teachers.index')->with('success', 'Teacher updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update teacher: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified teacher from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            // Find the teacher user
            $teacher = User::where('role', 2)->findOrFail($id);
            $teacherName = $teacher->name;

            // Delete the teacher (teacher details will be deleted via cascade)
            $teacher->delete();

            return redirect()->route('teachers.index')->with('success', "Teacher '{$teacherName}' deleted successfully!");
        } catch (\Exception $e) {
            return redirect()->route('teachers.index')->with('error', 'Failed to delete teacher: ' . $e->getMessage());
        }
    }

    /**
     * Update the teacher's profile picture.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateProfilePicture(Request $request, $id)
    {
        try {
            // Find the teacher user
            $teacher = User::where('role', 2)->findOrFail($id);

            // Validate the uploaded file
            $request->validate([
                'profile_picture' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            // Handle file upload
            if ($request->hasFile('profile_picture')) {
                // Delete old profile picture if exists
                if ($teacher->image && file_exists(public_path('storage/profile_pictures/' . $teacher->image))) {
                    unlink(public_path('storage/profile_pictures/' . $teacher->image));
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
                $teacher->image = $fileNameToStore;
                $teacher->save();

                return redirect()->route('teachers.show', $id)->with('success', 'Profile picture updated successfully!');
            }

            return redirect()->route('teachers.show', $id)->with('error', 'No profile picture uploaded!');
        } catch (\Exception $e) {
            return redirect()->route('teachers.show', $id)->with('error', 'Failed to update profile picture: ' . $e->getMessage());
        }
    }
}
