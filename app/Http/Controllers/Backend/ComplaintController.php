<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\User;
use App\Services\ActivityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ComplaintController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Complaint::with(['complainant', 'againstUser', 'responder']);

        // Show all complaints to admin
        if ($user->role === 1) {
            // No filter needed, show all complaints
        }
        // For teachers, show complaints they submitted or complaints against them
        else if ($user->role === 2) {
            $query->where(function ($q) use ($user) {
                $q->where('complainant_id', $user->id)
                    ->orWhere('against_user_id', $user->id);
            });
        }
        // For parents and students, show only their own complaints
        else {
            $query->where('complainant_id', $user->id);
        }

        $complaints = $query->latest()->get();
        $teachers = [];

        if (in_array($user->role, [3, 4])) { // Parent or Student
            $teachers = User::where('role', 2)->where('status', 'active')->get();
        }

        return view('backend.pages.complaints.index', compact('complaints', 'teachers'));
    }

    public function create()
    {
        $user = Auth::user();
        $teachers = [];

        if ($user->role === 3) { // Parent
            $teachers = User::where('role', 2)->where('status', 'active')->get();
        }

        return view('backend.pages.complaints.create', compact('teachers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
            'complaint_type' => 'required|in:against_teacher,against_admin,against_student,against_parent,general',
            'against_user_id' => 'required_if:complaint_type,against_teacher,against_student,against_parent|nullable|exists:users,id'
        ]);

        try {
            $complaint = new Complaint();
            $complaint->subject = $validated['subject'];
            $complaint->description = $validated['description'];
            $complaint->complaint_type = $validated['complaint_type'];
            $complaint->complainant_id = auth()->id();
            $complaint->complainant_type = $this->getUserType(auth()->user()->role);
            $complaint->against_user_id = $validated['against_user_id'] ?? null;
            $complaint->status = 'pending';
            $complaint->save();

            return redirect()
                ->route('complaints.index')
                ->with('success', 'Complaint submitted successfully.');
        } catch (\Exception $e) {
            Log::error('Error creating complaint', [
                'user_id' => auth()->id(),
                'request_data' => $request->all(),
                'error' => $e->getMessage()
            ]);

            return redirect()
                ->back()
                ->with('error', 'An error occurred while submitting your complaint. Please try again.')
                ->withInput();
        }
    }

    private function getUserType($role)
    {
        switch ($role) {
            case 2:
                return 'teacher';
            case 3:
                return 'parent';
            case 4:
                return 'student';
            default:
                return 'admin';
        }
    }

    public function show(Complaint $complaint)
    {
        $user = Auth::user();

        if ($user->role !== 1 && $complaint->complainant_id !== $user->id) {
            return redirect()->route('complaints.index')
                ->with('error', 'You do not have permission to view this complaint.');
        }

        return view('backend.pages.complaints.show', compact('complaint'));
    }

    public function update(Request $request, Complaint $complaint)
    {
        $user = Auth::user();

        if ($user->role !== 1) {
            return redirect()->route('complaints.index')
                ->with('error', 'Only administrators can update complaints.');
        }

        $request->validate([
            'status' => 'required|in:pending,in_progress,resolved,rejected',
            'response' => 'required|string'
        ]);

        $complaint->update([
            'status' => $request->status,
            'response' => $request->response,
            'responded_by' => $user->id,
            'resolved_at' => in_array($request->status, ['resolved', 'rejected']) ? now() : null
        ]);

        ActivityService::log(
            "Updated complaint status to {$request->status}: {$complaint->subject}",
            $user->id,
            'Updated Complaint'
        );

        return redirect()->route('complaints.show', $complaint)
            ->with('success', 'Complaint updated successfully.');
    }

    public function destroy(Complaint $complaint)
    {
        $user = Auth::user();

        if ($user->role !== 1 && $complaint->complainant_id !== $user->id) {
            return redirect()->route('complaints.index')
                ->with('error', 'You do not have permission to delete this complaint.');
        }

        if ($complaint->status !== 'pending') {
            return redirect()->route('complaints.index')
                ->with('error', 'Only pending complaints can be deleted.');
        }

        $complaint->delete();

        ActivityService::log(
            "Deleted complaint: {$complaint->subject}",
            $user->id,
            'Deleted Complaint'
        );

        return redirect()->route('complaints.index')
            ->with('success', 'Complaint deleted successfully.');
    }
}
