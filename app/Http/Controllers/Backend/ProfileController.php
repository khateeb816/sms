<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use App\Models\User;
use App\Models\Activity;
use App\Services\ActivityService;
use Illuminate\Database\Eloquent\Model;

class ProfileController extends Controller
{
    public function index()
    {
        $activities = Activity::where('user_id', auth()->id())
            ->latest()
            ->take(10)
            ->get();

        return view('backend.pages.profile.index', compact('activities'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:255'],
            'current_password' => ['required_with:password', 'current_password'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        // Update password if provided
        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        User::where('id', $user->id)->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'address' => $validated['address'],
            'password' => $validated['password'] ?? $user->password
        ]);

        // Log the activity
        ActivityService::log('Updated profile information', $user->id, 'update');

        return redirect()->back()->with('success', 'Profile updated successfully');
    }

    public function updatePhoto(Request $request)
    {
        try {
            $request->validate([
                'photo' => ['required', 'image', 'max:2048'], // 2MB max
            ]);

            $user = auth()->user();

            // Delete old image if exists
            if ($user->image) {
                Storage::delete('public/profile-images/' . $user->image);
            }

            // Store new image
            $image = $request->file('photo');
            $filename = time() . '_' . $user->id . '.' . $image->getClientOriginalExtension();

            // Ensure the directory exists
            if (!Storage::exists('public/profile-images')) {
                Storage::makeDirectory('public/profile-images');
            }

            // Store the image
            $path = $image->storeAs('public/profile-images', $filename);

            if (!$path) {
                throw new \Exception('Failed to store the image');
            }

            // Update user's image
            User::where('id', $user->id)->update([
                'image' => $filename
            ]);

            // Log the activity
            ActivityService::log('Updated profile photo', $user->id, 'update');

            return redirect()->back()->with('success', 'Profile photo updated successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update profile photo: ' . $e->getMessage());
        }
    }
}
