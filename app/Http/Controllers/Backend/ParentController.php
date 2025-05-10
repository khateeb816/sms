<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ActivityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

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
            $user->role = 3;  // 3 = parent
            $user->save();

            // Log the activity
            ActivityService::logParentActivity('Created', $user->name, $user->id);

            return redirect('/dash/parents')->with('success', 'Parent added successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to add parent: ' . $e->getMessage())->withInput();
        }
    }

    public function show($id)
    {
        // Find the parent user
        $parent = User::where('role', 3)->findOrFail($id);

        // Get all children associated with this parent
        $children = User::where('role', 4)->where('parent_id', $id)->get();

        return view('backend.pages.parents.show', compact('parent', 'children'));
    }

    public function edit($id)
    {
        // Find the parent user
        $parent = User::where('role', 3)->findOrFail($id);

        return view('backend.pages.parents.edit', compact('parent'));
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

            // Log the activity
            ActivityService::logParentActivity('Updated', $parent->name, $parent->id);

            return redirect('/dash/parents')->with('success', 'Parent updated successfully!');
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

            // Log the activity
            ActivityService::logParentActivity('Deleted', $parentName, $id);

            return redirect('/dash/parents')->with('success', "Parent '{$parentName}' deleted successfully!");
        } catch (\Exception $e) {
            return redirect('/dash/parents')->with('error', 'Failed to delete parent: ' . $e->getMessage());
        }
    }

    /**
     * Show form to add a child to a parent
     */
    public function addChildForm($id)
    {
        // Find the parent user
        $parent = User::where('role', 3)->findOrFail($id);

        // Get all students (users with role=1) who are not already children of this parent
        // Get all students who don't have this parent assigned
        $students = User::where('role', 4)  // role 4 = student
            ->where(function ($query) use ($id) {
                $query
                    ->whereNull('parent_id')
                    ->orWhere('parent_id', '!=', $id);
            })
            ->select('id', 'name', 'email', 'roll_number', 'class')
            ->get();

        return view('backend.pages.parents.add-child', compact('parent', 'students'));
    }

    /**
     * Process the add child form
     */
    public function addChild(Request $request, $id)
    {
        // Validate the request
        $request->validate([
            'student_id' => 'required|exists:users,id',
        ]);

        try {
            // Find the parent user
            $parent = User::where('role', 3)->findOrFail($id);

            // Find the student user
            $student = User::where('role', 4)->findOrFail($request->student_id);

            // Check if the relationship already exists
            if ($student->parent_id == $id) {
                return redirect()->back()->with('warning', 'This student is already associated with this parent!');
            }

            $student->parent_id = $id;
            $student->save();

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
            $student = User::where('role', 4)->findOrFail($childId);

            // Remove the relationship
            $student->parent_id = null;
            $student->save();

            return redirect()->route('parents.show', $parentId)->with('success', "Student '{$student->name}' removed successfully!");
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
