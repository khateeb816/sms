@extends('backend.layouts.app')

@section('title', 'Fine Details')

@section('content')
<div class="row pt-3">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">Fine Details</h4>
                <div>
                    @if(auth()->user()->role == 1)
                    <a href="{{ route('fines.edit', $fine->id) }}" class="btn btn-primary">
                        <i class="zmdi zmdi-edit"></i> Edit Fine
                    </a>
                    @endif
                    <a href="{{ route('fines.list') }}" class="btn btn-info">
                        <i class="zmdi zmdi-arrow-left"></i> Back to List
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h5 class="card-title">Student Information</h5>
                                <hr>
                                <p><strong>Name:</strong> {{ $fine->student->name }}</p>
                                <p><strong>Student ID:</strong> {{ $fine->student->id }}</p>
                                <p><strong>Class:</strong> {{ $fine->student->class->name ?? 'N/A' }}</p>
                                <p><strong>Roll Number:</strong> {{ $fine->student->roll_number ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h5 class="card-title">Fine Information</h5>
                                <hr>
                                <p><strong>Fine ID:</strong> {{ $fine->id }}</p>
                                <p><strong>Fine Type:</strong>
                                    @switch($fine->fine_type)
                                    @case('late_fee')
                                    Late Fee
                                    @break
                                    @case('library_fine')
                                    Library Fine
                                    @break
                                    @case('damage_fine')
                                    Damage Fine
                                    @break
                                    @case('disciplinary_fine')
                                    Disciplinary Fine
                                    @break
                                    @default
                                    Other
                                    @endswitch
                                </p>
                                <p><strong>Amount:</strong> PKR {{ number_format($fine->amount) }}</p>
                                <p><strong>Status:</strong>
                                    @if($fine->status == 'paid')
                                    <span class="badge badge-success">Paid</span>
                                    @elseif($fine->status == 'pending')
                                    <span class="badge badge-warning">Pending</span>
                                    @else
                                    <span class="badge badge-info">Waived</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-md-12">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h5 class="card-title">Additional Information</h5>
                                <hr>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>Issue Date:</strong> {{ $fine->issue_date->format('M d, Y') }}</p>
                                        <p><strong>Due Date:</strong> {{ $fine->due_date->format('M d, Y') }}</p>
                                        <p><strong>Days Remaining:</strong>
                                            @php
                                            $daysRemaining = now()->diffInDays($fine->due_date, false);
                                            @endphp
                                            @if($daysRemaining > 0)
                                            <span class="text-success">{{ $daysRemaining }} days</span>
                                            @elseif($daysRemaining == 0)
                                            <span class="text-warning">Due today</span>
                                            @else
                                            <span class="text-danger">Overdue by {{ abs($daysRemaining) }} days</span>
                                            @endif
                                        </p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Created By:</strong> {{ $fine->creator->name ?? 'N/A' }}</p>
                                        <p><strong>Created At:</strong> {{ $fine->created_at->format('M d, Y H:i:s') }}
                                        </p>
                                        <p><strong>Last Updated:</strong> {{ $fine->updated_at->format('M d, Y H:i:s')
                                            }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h5 class="card-title">Reason</h5>
                                <hr>
                                <p>{{ $fine->reason }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h5 class="card-title">Additional Notes</h5>
                                <hr>
                                <p>{{ $fine->notes ?? 'No additional notes' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                @if($fine->status == 'pending' && auth()->user()->role == 1)
                <div class="row mt-4">
                    <div class="col-md-12">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h5 class="card-title">Actions</h5>
                                <hr>
                                <form action="{{ route('fines.mark-paid', $fine->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-success"
                                        onclick="return confirm('Mark this fine as paid?')">
                                        <i class="zmdi zmdi-check"></i> Mark as Paid
                                    </button>
                                </form>
                                <form action="{{ route('fines.mark-waived', $fine->id) }}" method="POST"
                                    class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-warning"
                                        onclick="return confirm('Waive this fine?')">
                                        <i class="zmdi zmdi-block"></i> Waive Fine
                                    </button>
                                </form>
                                <form action="{{ route('fines.destroy', $fine->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger"
                                        onclick="return confirm('Are you sure you want to delete this fine?')">
                                        <i class="zmdi zmdi-delete"></i> Delete Fine
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection