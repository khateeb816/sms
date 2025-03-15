@extends('backend.layouts.app')

@section('title', 'Student Fees & Fines')

@section('content')
<div class="row pt-3">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">Fees & Fines for {{ $student->name }}</h4>
                <div>
                    <a href="{{ route('students.show', $student->id) }}" class="btn btn-info">
                        Student Profile
                    </a>
                    <a href="{{ route('fees.index') }}" class="btn btn-secondary">
                        Back to Fees Dashboard
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="card bg-primary">
                            <div class="card-body text-center">
                                <h5 class="text-white">Total Fees</h5>
                                <h3 class="text-white">PKR {{ number_format($fees->sum('amount'), 2) }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-danger">
                            <div class="card-body text-center">
                                <h5 class="text-white">Total Fines</h5>
                                <h3 class="text-white">PKR {{ number_format($fines->sum('amount'), 2) }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success">
                            <div class="card-body text-center">
                                <h5 class="text-white">Paid Amount</h5>
                                <h3 class="text-white">PKR {{ number_format($fees->where('status',
                                    'paid')->sum('amount') + $fines->where('status', 'paid')->sum('amount'), 2) }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning">
                            <div class="card-body text-center">
                                <h5 class="text-white">Pending Amount</h5>
                                <h3 class="text-white">PKR {{ number_format($fees->whereIn('status', ['pending',
                                    'overdue'])->sum('amount') + $fines->where('status', 'pending')->sum('amount'), 2)
                                    }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Fees Section -->
<div class="row mt-3">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title">Fees List</h5>
                <a href="{{ route('fees.create') }}?student_id={{ $student->id }}" class="btn btn-primary">
                    Add New Fee
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered datatable-fees">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Fee Type</th>
                                <th>Amount</th>
                                <th>Due Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($fees as $fee)
                            <tr>
                                <td>{{ $fee->id }}</td>
                                <td>{{ $fee->fee_type }}</td>
                                <td>PKR {{ number_format($fee->amount, 2) }}</td>
                                <td>{{ $fee->due_date->format('M d, Y') }}</td>
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
                                    <div class="btn-group">
                                        <a href="{{ route('fees.show', $fee->id) }}" class="btn btn-info btn-sm">
                                            View
                                        </a>
                                        <a href="{{ route('fees.edit', $fee->id) }}" class="btn btn-primary btn-sm">
                                            Edit
                                        </a>
                                        @if($fee->status != 'paid')
                                        <form action="{{ route('fees.mark-paid', $fee->id) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-sm"
                                                onclick="return confirm('Mark this fee as paid?')">
                                                Mark Paid
                                            </button>
                                        </form>
                                        @endif
                                        <form action="{{ route('fees.destroy', $fee->id) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm"
                                                onclick="return confirm('Are you sure you want to delete this fee?')">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
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

<!-- Fines Section -->
<div class="row mt-3">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title">Fines List</h5>
                <a href="{{ route('fines.create') }}?student_id={{ $student->id }}" class="btn btn-primary">
                    Add New Fine
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered datatable-fines">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Fine Type</th>
                                <th>Amount</th>
                                <th>Issue Date</th>
                                <th>Reason</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($fines as $fine)
                            <tr>
                                <td>{{ $fine->id }}</td>
                                <td>{{ $fine->fine_type }}</td>
                                <td>PKR {{ number_format($fine->amount, 2) }}</td>
                                <td>{{ $fine->issue_date->format('M d, Y') }}</td>
                                <td>{{ \Illuminate\Support\Str::limit($fine->reason, 30) }}</td>
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
                                    <div class="btn-group">
                                        <a href="{{ route('fines.show', $fine->id) }}" class="btn btn-info btn-sm">
                                            View
                                        </a>
                                        <a href="{{ route('fines.edit', $fine->id) }}" class="btn btn-primary btn-sm">
                                            Edit
                                        </a>
                                        @if($fine->status == 'pending')
                                        <form action="{{ route('fines.mark-paid', $fine->id) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-sm"
                                                onclick="return confirm('Mark this fine as paid?')">
                                                Mark Paid
                                            </button>
                                        </form>
                                        <form action="{{ route('fines.mark-waived', $fine->id) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-info btn-sm"
                                                onclick="return confirm('Waive this fine?')">
                                                Waive
                                            </button>
                                        </form>
                                        @endif
                                        <form action="{{ route('fines.destroy', $fine->id) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm"
                                                onclick="return confirm('Are you sure you want to delete this fine?')">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
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
        $('.datatable-fees').DataTable();
        $('.datatable-fines').DataTable();
    });
</script>
@endpush