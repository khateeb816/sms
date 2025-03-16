<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Fine;
use App\Models\User;
use App\Models\Activity;
use App\Models\Timetable;
use App\Services\ActivityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FineController extends Controller
{
    /**
     * Display a listing of the fines.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->role == 2) { // Teacher
            // For teachers, show fines they've created
            $issuedFines = Fine::with('student')
                ->where('created_by', $user->id)
                ->latest()
                ->get();

            // Empty collection for personal fines since teachers don't have personal fines
            $personalFines = collect();

            return view('backend.pages.fines.index', compact('issuedFines', 'personalFines'));
        }

        // For admin, show all fines with statistics
        $fines = Fine::with('student')->latest()->get();
        $pendingFines = Fine::where('status', 'pending')->count();
        $paidFines = Fine::where('status', 'paid')->count();
        $waivedFines = Fine::where('status', 'waived')->count();
        $totalFinesAmount = Fine::sum('amount');

        return view('backend.pages.fines.index', compact(
            'fines',
            'pendingFines',
            'paidFines',
            'waivedFines',
            'totalFinesAmount'
        ));
    }

    /**
     * Show the form for creating a new fine.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user = Auth::user();

        if ($user->role == 2) { // Teacher
            // Get class IDs from teacher's timetable
            $classIds = Timetable::where('teacher_id', $user->id)
                ->pluck('class_id')
                ->unique();

            // Get students from teacher's assigned classes using the pivot table
            $students = User::whereHas('classes', function ($query) use ($classIds) {
                $query->whereIn('class_rooms.id', $classIds);
            })
                ->where('role', 4)
                ->where('status', 'active')
                ->get();
        } else {
            // Admin can see all active students
            $students = User::where('role', 4)
                ->where('status', 'active')
                ->get();
        }

        return view('backend.pages.fines.create', compact('students'));
    }

    /**
     * Store a newly created fine in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'student_id' => 'required|exists:users,id',
            'fine_type' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'due_date' => 'required|date|after:today',
            'reason' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        if ($user->role == 2) {
            // Get class IDs from teacher's timetable
            $classIds = Timetable::where('teacher_id', $user->id)
                ->pluck('class_id')
                ->unique();

            // Verify if the student belongs to teacher's assigned classes
            $student = User::findOrFail($request->student_id);
            $studentClassIds = $student->classes()->pluck('class_rooms.id');

            if ($classIds->intersect($studentClassIds)->isEmpty()) {
                return back()->withErrors(['student_id' => 'You can only issue fines to students in your assigned classes.'])
                    ->withInput();
            }
        }

        $fine = Fine::create([
            'student_id' => $request->student_id,
            'fine_type' => $request->fine_type,
            'amount' => $request->amount,
            'issue_date' => now()->format('Y-m-d'),
            'due_date' => $request->due_date,
            'status' => 'pending',
            'reason' => $request->reason,
            'notes' => $request->notes,
            'created_by' => Auth::id()
        ]);

        // Log activity
        ActivityService::log("Created fine of PKR {$fine->amount} for student ID {$fine->student_id} - Due: {$fine->due_date}", Auth::id(), 'Created Fine');

        return redirect()->route('fines.list')->with('success', 'Fine created successfully.');
    }

    /**
     * Display the specified fine.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $fine = Fine::with('student')->findOrFail($id);
        return view('backend.pages.fines.show', compact('fine'));
    }

    /**
     * Show the form for editing the specified fine.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $fine = Fine::findOrFail($id);
        $students = User::where('role', 4)->where('status', 'active')->get();
        return view('backend.pages.fines.edit', compact('fine', 'students'));
    }

    /**
     * Update the specified fine in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'student_id' => 'required|exists:users,id',
            'fine_type' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'issue_date' => 'required|date',
            'status' => 'required|in:paid,pending,waived',
            'payment_date' => 'nullable|date|required_if:status,paid',
            'reason' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        $fine = Fine::findOrFail($id);
        $fine->update([
            'student_id' => $request->student_id,
            'fine_type' => $request->fine_type,
            'amount' => $request->amount,
            'issue_date' => $request->issue_date,
            'status' => $request->status,
            'payment_date' => $request->status == 'paid' ? $request->payment_date : null,
            'reason' => $request->reason,
            'notes' => $request->notes,
        ]);

        // Log activity
        ActivityService::log("Updated fine ID {$fine->id}", Auth::id(), 'Updated Fine');

        return redirect()->route('fines.list')->with('success', 'Fine updated successfully.');
    }

    /**
     * Remove the specified fine from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $fine = Fine::findOrFail($id);
        $fine->delete();

        // Log activity
        ActivityService::log("Deleted fine ID {$id}", Auth::id(), 'Deleted Fine');

        return redirect()->route('fines.list')->with('success', 'Fine deleted successfully.');
    }

    /**
     * Mark a fine as paid.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function markPaid($id)
    {
        $fine = Fine::findOrFail($id);
        $fine->update([
            'status' => 'paid',
            'payment_date' => now(),
        ]);

        // Log activity
        ActivityService::log("Marked fine ID {$id} as paid", Auth::id(), 'Marked Fine as Paid');

        return redirect()->back()->with('success', 'Fine marked as paid successfully.');
    }

    /**
     * Mark a fine as waived.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function markWaived($id)
    {
        $fine = Fine::findOrFail($id);
        $fine->update([
            'status' => 'waived',
        ]);

        // Log activity
        ActivityService::log("Waived fine ID {$id}", Auth::id(), 'Waived Fine');

        return redirect()->back()->with('success', 'Fine waived successfully.');
    }
}
