<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ParentController extends Controller
{
    public function index()
    {
        // Get all parents (users with role=3)
        $parents = User::where('role', 3)->get();
        return view('backend.pages.parents.index', compact('parents'));
    }

    public function create()
    {
        return view('backend.pages.parents.create');
    }

    public function store(Request $request)
    {
        // Validate the request
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:6|confirmed',
            'address' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        try {
            // Create a new user with role=3 (parent)
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->password = Hash::make($request->password);
            $user->address = $request->address;
            $user->status = $request->status;
            $user->role = 3; // 3 = parent
            $user->save();

            return redirect('/admin/parents')->with('success', 'Parent added successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to add parent: ' . $e->getMessage())->withInput();
        }
    }

    public function show($id)
    {
        try {
            // Find the parent user
            $parent = User::where('role', 3)->findOrFail($id);

            // Get all children associated with this parent
            $children = DB::table('parent_student')
                ->join('users', 'parent_student.student_id', '=', 'users.id')
                ->leftJoin('student_details', 'users.id', '=', 'student_details.user_id')
                ->where('parent_student.parent_id', $id)
                ->select(
                    'users.id',
                    'users.name',
                    'users.email',
                    'users.phone',
                    'users.address',
                    'users.image',
                    'users.status',
                    'student_details.class',
                    'student_details.roll_number'
                )
                ->get();

            return view('backend.pages.parents.show', compact('parent', 'children'));
        } catch (\Exception $e) {
            return redirect('/admin/parents')->with('error', 'Parent not found!');
        }
    }

    public function edit($id)
    {
        try {
            // Find the parent user
            $parent = User::where('role', 3)->findOrFail($id);

            return view('backend.pages.parents.edit', compact('parent'));
        } catch (\Exception $e) {
            return redirect('/admin/parents')->with('error', 'Parent not found!');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            // Find the parent user
            $parent = User::where('role', 3)->findOrFail($id);

            // Validate the request
            $rules = [
                'name' => 'required|string|max:255',
                'email' => [
                    'required',
                    'email',
                    Rule::unique('users')->ignore($parent->id),
                ],
                'phone' => 'required|string|max:20',
                'address' => 'nullable|string',
                'status' => 'required|in:active,inactive',
            ];

            // Add password validation if password is provided
            if ($request->filled('password')) {
                $rules['password'] = 'required|string|min:6|confirmed';
            }

            $request->validate($rules);

            // Update the parent
            $parent->name = $request->name;
            $parent->email = $request->email;
            $parent->phone = $request->phone;
            $parent->address = $request->address;
            $parent->status = $request->status;

            // Update password if provided
            if ($request->filled('password')) {
                $parent->password = Hash::make($request->password);
            }

            $parent->save();

            return redirect('/admin/parents')->with('success', 'Parent updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update parent: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            // Find the parent user
            $parent = User::where('role', 3)->findOrFail($id);
            $parentName = $parent->name;

            // Delete the parent
            $parent->delete();

            return redirect('/admin/parents')->with('success', "Parent '{$parentName}' deleted successfully!");
        } catch (\Exception $e) {
            return redirect('/admin/parents')->with('error', 'Failed to delete parent: ' . $e->getMessage());
        }
    }

    /**
     * Show form to add a child to a parent
     */
    public function addChildForm($id)
    {
        try {
            // Find the parent user
            $parent = User::where('role', 3)->findOrFail($id);

            // Get all students (users with role=1) who are not already children of this parent
            $students = DB::table('users')
                ->leftJoin('parent_student', function ($join) use ($id) {
                    $join->on('users.id', '=', 'parent_student.student_id')
                        ->where('parent_student.parent_id', '=', $id);
                })
                ->whereNull('parent_student.parent_id')
                ->where('users.role', 1)
                ->select('users.id', 'users.name', 'users.email')
                ->get();

            return view('backend.pages.parents.add_child', compact('parent', 'students'));
        } catch (\Exception $e) {
            return redirect('/admin/parents')->with('error', 'Parent not found!');
        }
    }

    /**
     * Process the add child form
     */
    public function addChild(Request $request, $id)
    {
        // Validate the request
        $request->validate([
            'student_id' => 'required|exists:users,id',
            'relationship' => 'required|string|max:50',
        ]);

        try {
            // Find the parent user
            $parent = User::where('role', 3)->findOrFail($id);

            // Find the student user
            $student = User::where('role', 1)->findOrFail($request->student_id);

            // Check if the relationship already exists
            $exists = DB::table('parent_student')
                ->where('parent_id', $id)
                ->where('student_id', $request->student_id)
                ->exists();

            if ($exists) {
                return redirect()->back()->with('warning', 'This student is already associated with this parent!');
            }

            // Create the relationship
            DB::table('parent_student')->insert([
                'parent_id' => $id,
                'student_id' => $request->student_id,
                'relationship' => $request->relationship,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return redirect()->route('parents.show', $id)->with('success', "Student '{$student->name}' added as a child successfully!");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to add child: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Remove a child from a parent
     */
    public function removeChild($parentId, $childId)
    {
        try {
            // Find the parent user
            $parent = User::where('role', 3)->findOrFail($parentId);

            // Find the student user
            $student = User::where('role', 1)->findOrFail($childId);

            // Remove the relationship
            $deleted = DB::table('parent_student')
                ->where('parent_id', $parentId)
                ->where('student_id', $childId)
                ->delete();

            if ($deleted) {
                return redirect()->route('parents.show', $parentId)->with('success', "Student '{$student->name}' removed successfully!");
            } else {
                return redirect()->route('parents.show', $parentId)->with('warning', 'No relationship found to remove!');
            }
        } catch (\Exception $e) {
            return redirect()->route('parents.show', $parentId)->with('error', 'Failed to remove child: ' . $e->getMessage());
        }
    }

    public function updateProfilePicture(Request $request, $id)
    {
        try {
            // Find the parent user
            $parent = User::where('role', 3)->findOrFail($id);

            // Validate the uploaded file
            $request->validate([
                'profile_picture' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            // Handle file upload
            if ($request->hasFile('profile_picture')) {
                // Delete old profile picture if exists
                if ($parent->image && file_exists(public_path('storage/profile_pictures/' . $parent->image))) {
                    unlink(public_path('storage/profile_pictures/' . $parent->image));
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
                $parent->image = $fileNameToStore;
                $parent->save();

                return redirect()->route('parents.show', $id)->with('success', 'Profile picture updated successfully!');
            }

            return redirect()->route('parents.show', $id)->with('error', 'No profile picture uploaded!');
        } catch (\Exception $e) {
            return redirect()->route('parents.show', $id)->with('error', 'Failed to update profile picture: ' . $e->getMessage());
        }
    }
}
