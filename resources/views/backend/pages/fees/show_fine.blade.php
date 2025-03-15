@extends('backend.layouts.app')

@section('title', 'Fine Details')

@section('content')
<div class="row pt-3">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">Fine Details</h4>
                <div>
                    <a href="{{ route('fines.edit', $fine->id) }}" class="btn btn-primary">
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
                                <th style="width: 30%">Fine ID</th>
                                <td>{{ $fine->id }}</td>
                            </tr>
                            <tr>
                                <th>Student</th>
                                <td>
                                    <a href="{{ route('students.show', $fine->student_id) }}">
                                        {{ $fine->student->name ?? 'N/A' }}
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <th>Fine Type</th>
                                <td>{{ $fine->fine_type }}</td>
                            </tr>
                            <tr>
                                <th>Amount</th>
                                <td>PKR {{ number_format($fine->amount, 2) }}</td>
                            </tr>
                            <tr>
                                <th>Issue Date</th>
                                <td>{{ $fine->issue_date->format('M d, Y') }}</td>
                            </tr>
                            <tr>
                                <th>Status</th>
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
                            @if($fine->status == 'paid')
                            <tr>
                                <th>Payment Date</th>
                                <td>{{ $fine->payment_date->format('M d, Y') }}</td>
                            </tr>
                            @endif
                            <tr>
                                <th>Created At</th>
                                <td>{{ $fine->created_at->format('M d, Y h:i A') }}</td>
                            </tr>
                            <tr>
                                <th>Last Updated</th>
                                <td>{{ $fine->updated_at->format('M d, Y h:i A') }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Reason</h5>
                            </div>
                            <div class="card-body">
                                <p>{{ $fine->reason }}</p>
                            </div>
                        </div>

                        @if($fine->notes)
                        <div class="card mt-3">
                            <div class="card-header">
                                <h5 class="card-title">Additional Notes</h5>
                            </div>
                            <div class="card-body">
                                <p>{{ $fine->notes }}</p>
                            </div>
                        </div>
                        @endif

                        @if($fine->status == 'pending')
                        <div class="card mt-3">
                            <div class="card-header">
                                <h5 class="card-title">Actions</h5>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('fines.mark-paid', $fine->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-success"
                                        onclick="return confirm('Mark this fine as paid?')">
                                        Mark as Paid
                                    </button>
                                </form>

                                <form action="{{ route('fines.mark-waived', $fine->id) }}" method="POST"
                                    class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-info"
                                        onclick="return confirm('Waive this fine?')">
                                        Waive Fine
                                    </button>
                                </form>

                                <form action="{{ route('fines.destroy', $fine->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger"
                                        onclick="return confirm('Are you sure you want to delete this fine?')">
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

@if($fine->student)
<div class="row mt-3">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Other Fines for {{ $fine->student->name }}</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered datatable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Fine Type</th>
                                <th>Amount</th>
                                <th>Issue Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($fine->student->fines()->where('id', '!=', $fine->id)->latest()->take(5)->get() as
                            $otherFine)
                            <tr>
                                <td>{{ $otherFine->id }}</td>
                                <td>{{ $otherFine->fine_type }}</td>
                                <td>PKR {{ number_format($otherFine->amount, 2) }}</td>
                                <td>{{ $otherFine->issue_date->format('M d, Y') }}</td>
                                <td>
                                    @if($otherFine->status == 'paid')
                                    <span class="badge badge-success">Paid</span>
                                    @elseif($otherFine->status == 'pending')
                                    <span class="badge badge-warning">Pending</span>
                                    @elseif($otherFine->status == 'waived')
                                    <span class="badge badge-info">Waived</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('fines.show', $otherFine->id) }}" class="btn btn-info btn-sm">
                                        View
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    <a href="{{ route('student.fees', $fine->student_id) }}" class="btn btn-primary">
                        View All Fees & Fines for {{ $fine->student->name }}
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