@extends('backend.layouts.app')

@section('title', 'Fee Details')

@section('content')
<div class="row pt-3">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">Fee Details</h4>
                <div>
                    <a href="{{ route('fees.edit', $fee->id) }}" class="btn btn-primary">
                        Edit
                    </a>
                    <a href="{{ route('fees.index') }}" class="btn btn-secondary">
                        Back to List
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-bordered">
                            <tr>
                                <th style="width: 30%">Fee ID</th>
                                <td>{{ $fee->id }}</td>
                            </tr>
                            <tr>
                                <th>Student</th>
                                <td>
                                    <a href="{{ route('students.show', $fee->student_id) }}">
                                        {{ $fee->student->name ?? 'N/A' }}
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <th>Fee Type</th>
                                <td>{{ $fee->fee_type }}</td>
                            </tr>
                            <tr>
                                <th>Amount</th>
                                <td>PKR {{ number_format($fee->amount, 2) }}</td>
                            </tr>
                            <tr>
                                <th>Due Date</th>
                                <td>{{ $fee->due_date->format('M d, Y') }}</td>
                            </tr>
                            <tr>
                                <th>Status</th>
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
                            @if($fee->status == 'paid')
                            <tr>
                                <th>Payment Date</th>
                                <td>{{ $fee->payment_date->format('M d, Y') }}</td>
                            </tr>
                            @endif
                            <tr>
                                <th>Created At</th>
                                <td>{{ $fee->created_at->format('M d, Y h:i A') }}</td>
                            </tr>
                            <tr>
                                <th>Last Updated</th>
                                <td>{{ $fee->updated_at->format('M d, Y h:i A') }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Description</h5>
                            </div>
                            <div class="card-body">
                                <p>{{ $fee->description ?? 'No description provided.' }}</p>
                            </div>
                        </div>

                        @if($fee->status != 'paid')
                        <div class="card mt-3">
                            <div class="card-header">
                                <h5 class="card-title">Actions</h5>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('fees.mark-paid', $fee->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-success"
                                        onclick="return confirm('Mark this fee as paid?')">
                                        Mark as Paid
                                    </button>
                                </form>

                                <form action="{{ route('fees.destroy', $fee->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger"
                                        onclick="return confirm('Are you sure you want to delete this fee?')">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if($fee->student)
<div class="row mt-3">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Other Fees for {{ $fee->student->name }}</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered datatable">
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
                            @foreach($fee->student->fees()->where('id', '!=', $fee->id)->latest()->take(5)->get() as
                            $otherFee)
                            <tr>
                                <td>{{ $otherFee->id }}</td>
                                <td>{{ $otherFee->fee_type }}</td>
                                <td>PKR {{ number_format($otherFee->amount, 2) }}</td>
                                <td>{{ $otherFee->due_date->format('M d, Y') }}</td>
                                <td>
                                    @if($otherFee->status == 'paid')
                                    <span class="badge badge-success">Paid</span>
                                    @elseif($otherFee->status == 'pending')
                                    <span class="badge badge-warning">Pending</span>
                                    @elseif($otherFee->status == 'overdue')
                                    <span class="badge badge-danger">Overdue</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('fees.show', $otherFee->id) }}" class="btn btn-info btn-sm">
                                        View
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    <a href="{{ route('student.fees', $fee->student_id) }}" class="btn btn-primary">
                        View All Fees & Fines for {{ $fee->student->name }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('.datatable').DataTable({
            "paging": false,
            "searching": false,
            "info": false
        });
    });
</script>
@endpush