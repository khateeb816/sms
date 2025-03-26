<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Fee;
use App\Models\Fine;
use App\Models\User;
use App\Services\ActivityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\ClassRoom;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class FeeController extends Controller
{
    /**
     * Display a listing of the fees.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->role == 3) { // Parent
            // Get all children of the parent
            $children = User::where('parent_id', $user->id)
                ->where('role', 4) // Student role
                ->where('status', 'active')
                ->get();

            // Get fees for children
            $fees = Fee::whereIn('student_id', $children->pluck('id'))
                ->with('student')
                ->latest()
                ->get();

            // Get fines for children
            $fines = Fine::whereIn('student_id', $children->pluck('id'))
                ->with('student')
                ->latest()
                ->get();

            $pendingFees = $fees->where('status', 'pending')->count();
            $paidFees = $fees->where('status', 'paid')->count();
            $overdueFees = $fees->where('status', 'overdue')->count();

            $pendingFines = $fines->where('status', 'pending')->count();
            $paidFines = $fines->where('status', 'paid')->count();
            $waivedFines = $fines->where('status', 'waived')->count();

            $totalFeesAmount = $fees->sum('amount');
            $totalFinesAmount = $fines->sum('amount');

            $students = $children;
        } else {
            // For admin and other roles, show all fees
            $fees = Fee::with('student')->latest()->get();
            $fines = Fine::with('student')->latest()->get();

            $pendingFees = Fee::where('status', 'pending')->count();
            $paidFees = Fee::where('status', 'paid')->count();
            $overdueFees = Fee::where('status', 'overdue')->count();

            $pendingFines = Fine::where('status', 'pending')->count();
            $paidFines = Fine::where('status', 'paid')->count();
            $waivedFines = Fine::where('status', 'waived')->count();

            $totalFeesAmount = Fee::sum('amount');
            $totalFinesAmount = Fine::sum('amount');

            $students = User::where('role', 4)->where('status', 'active')->get();
        }

        return view('backend.pages.fees.index', compact(
            'fees',
            'fines',
            'pendingFees',
            'paidFees',
            'overdueFees',
            'pendingFines',
            'paidFines',
            'waivedFines',
            'totalFeesAmount',
            'totalFinesAmount',
            'students'
        ));
    }

    /**
     * Display a listing of only the fees.
     *
     * @return \Illuminate\Http\Response
     */
    public function feesList()
    {
        $user = Auth::user();

        if ($user->role == 3) { // Parent
            // Get all children of the parent
            $children = User::where('parent_id', $user->id)
                ->where('role', 4) // Student role
                ->where('status', 'active')
                ->get();

            // Get fees for children
            $fees = Fee::whereIn('student_id', $children->pluck('id'))
                ->with('student')
                ->latest()
                ->get();

            $pendingFees = $fees->where('status', 'pending')->count();
            $paidFees = $fees->where('status', 'paid')->count();
            $overdueFees = $fees->where('status', 'overdue')->count();

            $totalFeesAmount = $fees->sum('amount');
        } else {
            // For admin and other roles, show all fees
            $fees = Fee::with('student')->latest()->get();

            $pendingFees = Fee::where('status', 'pending')->count();
            $paidFees = Fee::where('status', 'paid')->count();
            $overdueFees = Fee::where('status', 'overdue')->count();

            $totalFeesAmount = Fee::sum('amount');
        }

        return view('backend.pages.fees.fees_list', compact(
            'fees',
            'pendingFees',
            'paidFees',
            'overdueFees',
            'totalFeesAmount'
        ));
    }

    /**
     * Display a listing of only the fines.
     *
     * @return \Illuminate\Http\Response
     */
    public function finesList()
    {
        $user = Auth::user();

        if ($user->role == 3) { // Parent
            // Get all children of the parent
            $children = User::where('parent_id', $user->id)
                ->where('role', 4) // Student role
                ->where('status', 'active')
                ->get();

            // Get fines for children
            $fines = Fine::whereIn('student_id', $children->pluck('id'))
                ->with('student')
                ->latest()
                ->get();

            $pendingFines = $fines->where('status', 'pending')->count();
            $paidFines = $fines->where('status', 'paid')->count();
            $waivedFines = $fines->where('status', 'waived')->count();

            $totalFinesAmount = $fines->sum('amount');
        } else {
            // For admin and other roles, show all fines
            $fines = Fine::with('student')->latest()->get();

            $pendingFines = Fine::where('status', 'pending')->count();
            $paidFines = Fine::where('status', 'paid')->count();
            $waivedFines = Fine::where('status', 'waived')->count();

            $totalFinesAmount = Fine::sum('amount');
        }

        return view('backend.pages.fees.fines_list', compact(
            'fines',
            'pendingFines',
            'paidFines',
            'waivedFines',
            'totalFinesAmount'
        ));
    }

    /**
     * Show the form for creating a new fee.
     *
     * @return \Illuminate\Http\Response
     */
    public function createFee()
    {
        $students = User::where('role', 4)->where('status', 'active')->get();
        return view('backend.pages.fees.create', compact('students'));
    }

    /**
     * Store a newly created fee in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeFee(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:users,id',
            'fee_type' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'due_date' => 'required|date',
            'description' => 'nullable|string',
        ]);

        $fee = Fee::create([
            'student_id' => $request->student_id,
            'fee_type' => $request->fee_type,
            'amount' => $request->amount,
            'due_date' => $request->due_date,
            'status' => 'pending',
            'description' => $request->description,
        ]);

        // Log the activity
        ActivityService::logFeeActivity('Created', $fee->fee_type, $fee->amount, $fee->student_id);

        return redirect()->route('fees.list')->with('success', 'Fee created successfully.');
    }

    /**
     * Display the specified fee.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showFee($id)
    {
        $fee = Fee::with('student')->findOrFail($id);
        return view('backend.pages.fees.show', compact('fee'));
    }

    /**
     * Show the form for editing the specified fee.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function editFee($id)
    {
        $fee = Fee::findOrFail($id);
        $students = User::where('role', 4)->where('status', 'active')->get();
        return view('backend.pages.fees.edit', compact('fee', 'students'));
    }

    /**
     * Update the specified fee in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateFee(Request $request, $id)
    {
        $request->validate([
            'student_id' => 'required|exists:users,id',
            'fee_type' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'due_date' => 'required|date',
            'status' => 'required|in:paid,pending,overdue',
            'payment_date' => 'nullable|date|required_if:status,paid',
            'description' => 'nullable|string',
        ]);

        $fee = Fee::findOrFail($id);
        $fee->update([
            'student_id' => $request->student_id,
            'fee_type' => $request->fee_type,
            'amount' => $request->amount,
            'due_date' => $request->due_date,
            'status' => $request->status,
            'payment_date' => $request->status == 'paid' ? $request->payment_date : null,
            'description' => $request->description,
        ]);

        // Log the activity
        ActivityService::logFeeActivity('Updated', $fee->fee_type, $fee->amount, $fee->student_id);

        return redirect()->route('fees.list')->with('success', 'Fee updated successfully.');
    }

    /**
     * Remove the specified fee from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroyFee($id)
    {
        $fee = Fee::findOrFail($id);
        $feeType = $fee->fee_type;
        $feeAmount = $fee->amount;
        $studentId = $fee->student_id;
        $fee->delete();

        // Log the activity
        ActivityService::logFeeActivity('Deleted', $feeType, $feeAmount, $studentId);

        return redirect()->route('fees.list')->with('success', 'Fee deleted successfully.');
    }

    /**
     * Mark the specified fee as paid.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function markFeePaid($id)
    {
        $fee = Fee::findOrFail($id);
        $fee->update([
            'status' => 'paid',
            'payment_date' => now(),
        ]);

        // Log the activity
        ActivityService::logFeeActivity('Marked as paid', $fee->fee_type, $fee->amount, $fee->student_id);

        return redirect()->route('fees.list')->with('success', 'Fee marked as paid.');
    }

    /**
     * Show the form for creating a new fine.
     *
     * @return \Illuminate\Http\Response
     */
    public function createFine()
    {
        $students = User::where('role', 4)->where('status', 'active')->get();
        return view('backend.pages.fees.create_fine', compact('students'));
    }

    /**
     * Store a newly created fine in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeFine(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:users,id',
            'fine_type' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'issue_date' => 'required|date',
            'reason' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        $fine = Fine::create([
            'student_id' => $request->student_id,
            'fine_type' => $request->fine_type,
            'amount' => $request->amount,
            'issue_date' => $request->issue_date,
            'status' => 'pending',
            'reason' => $request->reason,
            'notes' => $request->notes,
        ]);

        // Log the activity
        ActivityService::logFineActivity('Created', $fine->fine_type, $fine->amount, $fine->student_id);

        return redirect()->route('fines.list')->with('success', 'Fine created successfully.');
    }

    /**
     * Display the specified fine.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showFine($id)
    {
        $fine = Fine::with('student')->findOrFail($id);
        return view('backend.pages.fees.show_fine', compact('fine'));
    }

    /**
     * Show the form for editing the specified fine.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function editFine($id)
    {
        $fine = Fine::findOrFail($id);
        $students = User::where('role', 4)->where('status', 'active')->get();
        return view('backend.pages.fees.edit_fine', compact('fine', 'students'));
    }

    /**
     * Update the specified fine in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateFine(Request $request, $id)
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

        // Log the activity
        ActivityService::logFineActivity('Updated', $fine->fine_type, $fine->amount, $fine->student_id);

        return redirect()->route('fines.list')->with('success', 'Fine updated successfully.');
    }

    /**
     * Remove the specified fine from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroyFine($id)
    {
        $fine = Fine::findOrFail($id);
        $fineType = $fine->fine_type;
        $fineAmount = $fine->amount;
        $studentId = $fine->student_id;
        $fine->delete();

        // Log the activity
        ActivityService::logFineActivity('Deleted', $fineType, $fineAmount, $studentId);

        return redirect()->route('fines.list')->with('success', 'Fine deleted successfully.');
    }

    /**
     * Mark the specified fine as paid.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function markFinePaid($id)
    {
        $fine = Fine::findOrFail($id);
        $fine->update([
            'status' => 'paid',
            'payment_date' => now(),
        ]);

        // Log the activity
        ActivityService::logFineActivity('Marked as paid', $fine->fine_type, $fine->amount, $fine->student_id);

        return redirect()->route('fines.list')->with('success', 'Fine marked as paid.');
    }

    /**
     * Mark the specified fine as waived.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function markFineWaived($id)
    {
        $fine = Fine::findOrFail($id);
        $fine->update([
            'status' => 'waived',
        ]);

        // Log the activity
        ActivityService::logFineActivity('Waived', $fine->fine_type, $fine->amount, $fine->student_id);

        return redirect()->route('fines.list')->with('success', 'Fine waived successfully.');
    }

    /**
     * Display the student's fees and fines.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function studentFees($id)
    {
        $student = User::findOrFail($id);
        $fees = Fee::where('student_id', $id)->latest()->get();
        $fines = Fine::where('student_id', $id)->latest()->get();

        return view('backend.pages.fees.student', compact('student', 'fees', 'fines'));
    }

    /**
     * Check for overdue fees and update their status.
     *
     * @return \Illuminate\Http\Response
     */
    public function checkOverdueFees()
    {
        $overdueFees = Fee::where('due_date', '<', now())
            ->where('status', 'pending')
            ->update(['status' => 'overdue']);

        return redirect()->route('fees.list')->with('success', 'Overdue fees checked and updated.');
    }

    /**
     * Generate a report of fees and fines.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function report(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'report_type' => 'required|in:fees,fines,both',
        ]);

        $startDate = $request->start_date;
        $endDate = $request->end_date;
        $reportType = $request->report_type;

        // Add one day to end date to include the end date in the results
        $endDateForQuery = date('Y-m-d', strtotime($endDate . ' +1 day'));

        $fees = collect();
        $fines = collect();

        if ($reportType == 'fees' || $reportType == 'both') {
            $fees = Fee::with('student')
                ->whereBetween('created_at', [$startDate, $endDateForQuery])
                ->latest()
                ->get();
        }

        if ($reportType == 'fines' || $reportType == 'both') {
            $fines = Fine::with('student')
                ->whereBetween('created_at', [$startDate, $endDateForQuery])
                ->latest()
                ->get();
        }

        // Log the activity
        ActivityService::logReportActivity($reportType, $startDate, $endDate);

        return view('backend.pages.fees.report', compact('fees', 'fines', 'startDate', 'endDate', 'reportType'));
    }

    /**
     * Simple report generation with minimal validation for direct URL access.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function generateReport(Request $request)
    {
        // Default values if not provided
        $startDate = $request->start_date ?? date('Y-m-01');
        $endDate = $request->end_date ?? date('Y-m-t');
        $reportType = $request->report_type ?? 'both';

        // Ensure report type is valid
        if (!in_array($reportType, ['fees', 'fines', 'both'])) {
            $reportType = 'both';
        }

        // Add one day to end date to include the end date in the results
        $endDateForQuery = date('Y-m-d', strtotime($endDate . ' +1 day'));

        $fees = collect();
        $fines = collect();

        if ($reportType == 'fees' || $reportType == 'both') {
            $fees = Fee::with('student')
                ->whereBetween('created_at', [$startDate, $endDateForQuery])
                ->latest()
                ->get();
        }

        if ($reportType == 'fines' || $reportType == 'both') {
            $fines = Fine::with('student')
                ->whereBetween('created_at', [$startDate, $endDateForQuery])
                ->latest()
                ->get();
        }

        // Log the activity
        ActivityService::logReportActivity($reportType, $startDate, $endDate);

        return view('backend.pages.fees.report', compact('fees', 'fines', 'startDate', 'endDate', 'reportType'));
    }

    /**
     * Public report generation that doesn't require authentication.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function publicReport(Request $request)
    {
        // Default values if not provided
        $startDate = $request->start_date ?? date('Y-m-01');
        $endDate = $request->end_date ?? date('Y-m-t');
        $reportType = $request->report_type ?? 'both';
        $feeStatus = $request->fee_status ?? 'all';
        $fineStatus = $request->fine_status ?? 'all';
        $studentSearch = $request->student_search ?? '';

        // Ensure report type is valid
        if (!in_array($reportType, ['fees', 'fines', 'both'])) {
            $reportType = 'both';
        }

        // Add one day to end date to include the end date in the results
        $endDateForQuery = date('Y-m-d', strtotime($endDate . ' +1 day'));

        $fees = collect();
        $fines = collect();

        if ($reportType == 'fees' || $reportType == 'both') {
            // Start with base query
            $feesQuery = Fee::with('student')
                ->whereBetween('created_at', [$startDate, $endDateForQuery]);

            // Apply fee status filter if not 'all'
            if ($feeStatus !== 'all') {
                $feesQuery->where('status', $feeStatus);
            }

            // Apply student search filter if provided
            if (!empty($studentSearch)) {
                $feesQuery->whereHas('student', function ($query) use ($studentSearch) {
                    $query->where('name', 'like', '%' . $studentSearch . '%')
                        ->orWhere('id', 'like', '%' . $studentSearch . '%')
                        ->orWhere('roll_number', 'like', '%' . $studentSearch . '%');
                });
            }

            // Get the results
            $fees = $feesQuery->latest()->get();
        }

        if ($reportType == 'fines' || $reportType == 'both') {
            // Start with base query
            $finesQuery = Fine::with('student')
                ->whereBetween('created_at', [$startDate, $endDateForQuery]);

            // Apply fine status filter if not 'all'
            if ($fineStatus !== 'all') {
                $finesQuery->where('status', $fineStatus);
            }

            // Apply student search filter if provided
            if (!empty($studentSearch)) {
                $finesQuery->whereHas('student', function ($query) use ($studentSearch) {
                    $query->where('name', 'like', '%' . $studentSearch . '%')
                        ->orWhere('id', 'like', '%' . $studentSearch . '%')
                        ->orWhere('roll_number', 'like', '%' . $studentSearch . '%');
                });
            }

            // Get the results
            $fines = $finesQuery->latest()->get();
        }

        // Build filters string for activity log
        $filters = [];
        if ($feeStatus !== 'all') {
            $filters[] = "fee status: {$feeStatus}";
        }
        if ($fineStatus !== 'all') {
            $filters[] = "fine status: {$fineStatus}";
        }
        if (!empty($studentSearch)) {
            $filters[] = "student search: {$studentSearch}";
        }

        // Log the activity
        ActivityService::logReportActivity(
            $reportType,
            $startDate,
            $endDate,
            !empty($filters) ? implode(', ', $filters) : ''
        );

        return view('backend.pages.fees.report', compact(
            'fees',
            'fines',
            'startDate',
            'endDate',
            'reportType',
            'feeStatus',
            'fineStatus',
            'studentSearch'
        ));
    }

    public function printReport(Request $request)
    {
        $user = Auth::user();
        $classId = $request->input('class_id');
        $month = $request->input('month', now()->format('Y-m'));

        $class = ClassRoom::findOrFail($classId);
        $students = $class->students()->with(['fees' => function($query) use ($month) {
            $query->whereMonth('date', Carbon::parse($month)->month)
                  ->whereYear('date', Carbon::parse($month)->year);
        }])->get();

        $pdf = Pdf::loadView('backend.pages.fees.print-report', compact('class', 'students', 'month'));

        ActivityService::log("Printed fee report for class: {$class->name} ({$month})", $user->id, 'fee');

        return $pdf->download("fee-report-{$class->name}-{$month}.pdf");
    }

    /**
     * Display fees and fines for parent's children.
     *
     * @return \Illuminate\Http\Response
     */
    public function parentFees()
    {
        $user = Auth::user();

        if ($user->role != 3) {
            return redirect()->route('fees.index')->with('error', 'Unauthorized access.');
        }

        // Get all children of the parent
        $children = User::where('parent_id', $user->id)
            ->where('role', 4) // Student role
            ->where('status', 'active')
            ->get();

        // Get fees for children
        $fees = Fee::whereIn('student_id', $children->pluck('id'))
            ->with('student')
            ->latest()
            ->get();

        // Get fines for children
        $fines = Fine::whereIn('student_id', $children->pluck('id'))
            ->with('student')
            ->latest()
            ->get();

        return view('backend.pages.fees.parent_fees', compact('children', 'fees', 'fines'));
    }
}
