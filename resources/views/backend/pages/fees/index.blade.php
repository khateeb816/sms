@extends('backend.layouts.app')

@section('title', 'Fees & Fines Management')

@section('content')
<div class="row pt-3">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">Fees & Fines Dashboard</h4>
                <div>
                    <a href="/dash/public-report?start_date={{ date('Y-m-01') }}&end_date={{ date('Y-m-t') }}&report_type=both"
                        class="btn btn-info">
                        <i class="fa fa-file-text mr-1"></i> Generate Report
                    </a>
                    <form action="{{ route('fees.check-overdue') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-warning">
                            <i class="fa fa-clock-o mr-1"></i> Check Overdue Fees
                        </button>
                    </form>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Fee Stats -->
                    <div class="col-md-6">
                        <div class="card bg-primary">
                            <div class="card-body">
                                <div class="media">
                                    <div class="media-body">
                                        <h5 class="text-white">Total Fees</h5>
                                        <h4 class="text-white">PKR {{ number_format($totalFeesAmount, 2) }}</h4>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fa fa-money text-white fa-3x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Fine Stats -->
                    <div class="col-md-6">
                        <div class="card bg-danger">
                            <div class="card-body">
                                <div class="media">
                                    <div class="media-body">
                                        <h5 class="text-white">Total Fines</h5>
                                        <h4 class="text-white">PKR {{ number_format($totalFinesAmount, 2) }}</h4>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fa fa-exclamation-triangle text-white fa-3x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <!-- Fee Status -->
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="card bg-success">
                                    <div class="card-body p-3">
                                        <div class="text-center">
                                            <h5 class="text-white">Paid Fees</h5>
                                            <h4 class="text-white">{{ $paidFees }}</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-warning">
                                    <div class="card-body p-3">
                                        <div class="text-center">
                                            <h5 class="text-white">Pending Fees</h5>
                                            <h4 class="text-white">{{ $pendingFees }}</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-danger">
                                    <div class="card-body p-3">
                                        <div class="text-center">
                                            <h5 class="text-white">Overdue Fees</h5>
                                            <h4 class="text-white">{{ $overdueFees }}</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Fine Status -->
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="card bg-success">
                                    <div class="card-body p-3">
                                        <div class="text-center">
                                            <h5 class="text-white">Paid Fines</h5>
                                            <h4 class="text-white">{{ $paidFines }}</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-warning">
                                    <div class="card-body p-3">
                                        <div class="text-center">
                                            <h5 class="text-white">Pending Fines</h5>
                                            <h4 class="text-white">{{ $pendingFines }}</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-info">
                                    <div class="card-body p-3">
                                        <div class="text-center">
                                            <h5 class="text-white">Waived Fines</h5>
                                            <h4 class="text-white">{{ $waivedFines }}</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-3">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">Quick Actions</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body text-center">
                                <h5 class="card-title">Fees Management</h5>
                                <p>View, add, edit, and manage all student fees</p>
                                <div class="mt-3">
                                    <a href="{{ route('fees.list') }}" class="btn btn-primary btn-lg">
                                        <i class="fa fa-money mr-2"></i> Manage Fees
                                    </a>
                                    <a href="{{ route('fees.create') }}" class="btn btn-success btn-lg mt-2">
                                        <i class="fa fa-plus mr-2"></i> Add New Fee
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body text-center">
                                <h5 class="card-title">Fines Management</h5>
                                <p>View, add, edit, and manage all student fines</p>
                                <div class="mt-3">
                                    <a href="{{ route('fines.list') }}" class="btn btn-danger btn-lg">
                                        <i class="fa fa-exclamation-triangle mr-2"></i> Manage Fines
                                    </a>
                                    <a href="{{ route('fines.create') }}" class="btn btn-success btn-lg mt-2">
                                        <i class="fa fa-plus mr-2"></i> Add New Fine
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Report Options Card -->
<div class="row mt-3">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Generate Custom Reports</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body text-center">
                                <h5 class="card-title">Fees Report</h5>
                                <p>Generate a report of all fees for the selected period</p>
                                <a href="/dash/public-report?start_date={{ date('Y-m-01') }}&end_date={{ date('Y-m-t') }}&report_type=fees"
                                    class="btn btn-primary">
                                    <i class="fa fa-file-text mr-1"></i> Fees Report
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body text-center">
                                <h5 class="card-title">Fines Report</h5>
                                <p>Generate a report of all fines for the selected period</p>
                                <a href="/dash/public-report?start_date={{ date('Y-m-01') }}&end_date={{ date('Y-m-t') }}&report_type=fines"
                                    class="btn btn-danger">
                                    <i class="fa fa-file-text mr-1"></i> Fines Report
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body text-center">
                                <h5 class="card-title">Combined Report</h5>
                                <p>Generate a report of both fees and fines</p>
                                <a href="/dash/public-report?start_date={{ date('Y-m-01') }}&end_date={{ date('Y-m-t') }}&report_type=both"
                                    class="btn btn-info">
                                    <i class="fa fa-file-text mr-1"></i> Combined Report
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-12">
                        <div class="alert alert-info">
                            <p><strong>Note:</strong> The reports above are for the current month. For custom date
                                ranges, use the links and modify the date parameters in the URL.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Fees and Fines -->
<div class="row mt-3">
    <!-- Recent Fees -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title">Recent Fees</h5>
                <a href="{{ route('fees.list') }}" class="btn btn-sm btn-primary">View All</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Student</th>
                                <th>Fee Type</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($fees->take(5) as $fee)
                            <tr>
                                <td>{{ $fee->id }}</td>
                                <td>{{ $fee->student->name ?? 'N/A' }}</td>
                                <td>{{ $fee->fee_type }}</td>
                                <td>PKR {{ number_format($fee->amount, 2) }}</td>
                                <td>
                                    @if($fee->status == 'paid')
                                    <span class="badge badge-success">Paid</span>
                                    @elseif($fee->status == 'pending')
                                    <span class="badge badge-warning">Pending</span>
                                    @elseif($fee->status == 'overdue')
                                    <span class="badge badge-danger">Overdue</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('fees.show', $fee->id) }}" class="btn btn-info btn-sm">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                    <a href="{{ route('fees.edit', $fee->id) }}" class="btn btn-primary btn-sm">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Fines -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title">Recent Fines</h5>
                <a href="{{ route('fines.list') }}" class="btn btn-sm btn-danger">View All</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Student</th>
                                <th>Fine Type</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($fines->take(5) as $fine)
                            <tr>
                                <td>{{ $fine->id }}</td>
                                <td>{{ $fine->student->name ?? 'N/A' }}</td>
                                <td>{{ $fine->fine_type }}</td>
                                <td>PKR {{ number_format($fine->amount, 2) }}</td>
                                <td>
                                    @if($fine->status == 'paid')
                                    <span class="badge badge-success">Paid</span>
                                    @elseif($fine->status == 'pending')
                                    <span class="badge badge-warning">Pending</span>
                                    @elseif($fine->status == 'waived')
                                    <span class="badge badge-info">Waived</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('fines.show', $fine->id) }}" class="btn btn-info btn-sm">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                    <a href="{{ route('fines.edit', $fine->id) }}" class="btn btn-primary btn-sm">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Students with Pending Fees/Fines -->
<div class="row mt-3">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Students with Pending Fees/Fines</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Student ID</th>
                                <th>Student Name</th>
                                <th>Pending Fees</th>
                                <th>Pending Fines</th>
                                <th>Total Pending Amount</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                            $studentsWithPending = [];

                            // Group fees by student
                            foreach($fees->where('status', '!=', 'paid') as $fee) {
                            if (!isset($studentsWithPending[$fee->student_id])) {
                            $studentsWithPending[$fee->student_id] = [
                            'student' => $fee->student,
                            'pending_fees' => 0,
                            'pending_fines' => 0
                            ];
                            }
                            $studentsWithPending[$fee->student_id]['pending_fees'] += $fee->amount;
                            }

                            // Group fines by student
                            foreach($fines->where('status', 'pending') as $fine) {
                            if (!isset($studentsWithPending[$fine->student_id])) {
                            $studentsWithPending[$fine->student_id] = [
                            'student' => $fine->student,
                            'pending_fees' => 0,
                            'pending_fines' => 0
                            ];
                            }
                            $studentsWithPending[$fine->student_id]['pending_fines'] += $fine->amount;
                            }
                            @endphp

                            @foreach($studentsWithPending as $studentId => $data)
                            @php
                            $totalPending = $data['pending_fees'] + $data['pending_fines'];
                            @endphp
                            <tr>
                                <td>{{ $studentId }}</td>
                                <td>{{ $data['student']->name ?? 'N/A' }}</td>
                                <td>PKR {{ number_format($data['pending_fees'], 2) }}</td>
                                <td>PKR {{ number_format($data['pending_fines'], 2) }}</td>
                                <td>PKR {{ number_format($totalPending, 2) }}</td>
                                <td>
                                    <a href="{{ route('student.fees', $studentId) }}" class="btn btn-primary btn-sm">
                                        <i class="fa fa-eye mr-1"></i> View Details
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Initialize DataTables for all tables
        $('.table').DataTable({
            "responsive": true,
            "autoWidth": false,
            "pageLength": 5,
            "lengthMenu": [5, 10, 25, 50],
            "language": {
                "lengthMenu": "Show _MENU_ entries",
                "zeroRecords": "No records found",
                "info": "Showing page _PAGE_ of _PAGES_",
                "infoEmpty": "No records available",
                "infoFiltered": "(filtered from _MAX_ total records)"
            }
        });
    });
</script>
@endpush