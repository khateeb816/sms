@extends('backend.layouts.app')

@section('title', 'Fees Management')

@section('content')
<div class="row pt-3">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">Fees Management</h4>
                <div>
                    <a href="{{ route('fees.index') }}" class="btn btn-secondary">
                        Back to Dashboard
                    </a>
                    <a href="{{ route('fines.list') }}" class="btn btn-info">
                        View Fines
                    </a>
                    <a href="{{ route('fees.create') }}" class="btn btn-primary">
                        Add New Fee
                    </a>
                    <form action="{{ route('fees.check-overdue') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-warning">
                            Check Overdue Fees
                        </button>
                    </form>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <!-- Fee Stats -->
                    <div class="col-md-3">
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

                    <div class="col-md-3">
                        <div class="card bg-success">
                            <div class="card-body p-3">
                                <div class="text-center">
                                    <h5 class="text-white">Paid Fees</h5>
                                    <h4 class="text-white">{{ $paidFees }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning">
                            <div class="card-body p-3">
                                <div class="text-center">
                                    <h5 class="text-white">Pending Fees</h5>
                                    <h4 class="text-white">{{ $pendingFees }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
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

                <div class="table-responsive">
                    <table class="table table-bordered datatable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Student</th>
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
                                <td>{{ $fee->student->name ?? 'N/A' }}</td>
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
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Initialize DataTables
        $('.datatable').DataTable();
    });
</script>
@endpush