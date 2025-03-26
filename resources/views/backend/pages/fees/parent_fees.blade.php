@extends('backend.layouts.app')

@section('title', 'My Children\'s Fees & Fines')

@section('content')
<div class="row pt-3">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">My Children's Fees & Fines Overview</h4>
            </div>
            <div class="card-body">
                <!-- Summary Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-primary">
                            <div class="card-body text-center">
                                <h5 class="text-white">Total Pending</h5>
                                <h3 class="text-white">PKR {{ number_format($fees->whereIn('status', ['pending', 'overdue'])->sum('amount') + $fines->where('status', 'pending')->sum('amount'), 2) }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success">
                            <div class="card-body text-center">
                                <h5 class="text-white">Total Paid</h5>
                                <h3 class="text-white">PKR {{ number_format($fees->where('status', 'paid')->sum('amount') + $fines->where('status', 'paid')->sum('amount'), 2) }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning">
                            <div class="card-body text-center">
                                <h5 class="text-white">Overdue Fees</h5>
                                <h3 class="text-white">PKR {{ number_format($fees->where('status', 'overdue')->sum('amount'), 2) }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info">
                            <div class="card-body text-center">
                                <h5 class="text-white">Pending Fines</h5>
                                <h3 class="text-white">PKR {{ number_format($fines->where('status', 'pending')->sum('amount'), 2) }}</h3>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Children's Fees & Fines -->
                @foreach($children as $child)
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h5 class="card-title mb-0">{{ $child->name }}</h5>
                    </div>
                    <div class="card-body">
                        <!-- Fees Table -->
                        <h6 class="mb-3">Fees</h6>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Fee Type</th>
                                        <th>Amount</th>
                                        <th>Due Date</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($fees->where('student_id', $child->id) as $fee)
                                    <tr>
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
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Fines Table -->
                        <h6 class="mb-3">Fines</h6>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Fine Type</th>
                                        <th>Amount</th>
                                        <th>Issue Date</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($fines->where('student_id', $child->id) as $fine)
                                    <tr>
                                        <td>{{ $fine->fine_type }}</td>
                                        <td>PKR {{ number_format($fine->amount, 2) }}</td>
                                        <td>{{ $fine->issue_date->format('M d, Y') }}</td>
                                        <td>
                                            @if($fine->status == 'paid')
                                            <span class="badge badge-success">Paid</span>
                                            @elseif($fine->status == 'pending')
                                            <span class="badge badge-warning">Pending</span>
                                            @elseif($fine->status == 'waived')
                                            <span class="badge badge-info">Waived</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Initialize any necessary scripts
    });
</script>
@endpush