@extends('backend.layouts.app')

@section('title', 'Fines Management')

@section('content')
<div class="row pt-3">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">Fines Management</h4>
                <div>
                    <a href="{{ route('fees.index') }}" class="btn btn-secondary">
                        Back to Dashboard
                    </a>
                    <a href="{{ route('fees.list') }}" class="btn btn-info">
                        View Fees
                    </a>
                    <a href="{{ route('fines.create') }}" class="btn btn-primary">
                        Add New Fine
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <!-- Fine Stats -->
                    <div class="col-md-3">
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

                    <div class="col-md-3">
                        <div class="card bg-success">
                            <div class="card-body p-3">
                                <div class="text-center">
                                    <h5 class="text-white">Paid Fines</h5>
                                    <h4 class="text-white">{{ $paidFines }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning">
                            <div class="card-body p-3">
                                <div class="text-center">
                                    <h5 class="text-white">Pending Fines</h5>
                                    <h4 class="text-white">{{ $pendingFines }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
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

                <div class="table-responsive">
                    <table class="table table-bordered datatable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Student</th>
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
                                <td>{{ $fine->student->name ?? 'N/A' }}</td>
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
        // Initialize DataTables
        $('.datatable').DataTable();
    });
</script>
@endpush