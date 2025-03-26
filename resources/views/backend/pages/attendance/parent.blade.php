@extends('backend.layouts.app')

@section('title', 'Children\'s Attendance')

@section('content')
<div class="row pt-3">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">Children's Attendance</h4>
            </div>
            <div class="card-body">
                <!-- Date Selection -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header bg-light">
                                <h5 class="card-title mb-0">
                                    <i class="fa fa-calendar mr-2"></i>Select Date
                                </h5>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('attendance.parent.index') }}" method="GET" id="filterForm">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="date">Date</label>
                                                <input type="date" class="form-control" id="date" name="date"
                                                    value="{{ $selectedDate->format('Y-m-d') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>&nbsp;</label>
                                                <button type="submit" class="btn btn-primary btn-block">
                                                    <i class="fa fa-search mr-1"></i> Load Attendance
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Attendance Statistics -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <h5 class="card-title">Present</h5>
                                <h2 class="mb-0">{{ $attendanceStats['present'] }}%</h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-danger text-white">
                            <div class="card-body">
                                <h5 class="card-title">Absent</h5>
                                <h2 class="mb-0">{{ $attendanceStats['absent'] }}%</h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body">
                                <h5 class="card-title">Late</h5>
                                <h2 class="mb-0">{{ $attendanceStats['late'] }}%</h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <h5 class="card-title">Leave</h5>
                                <h2 class="mb-0">{{ $attendanceStats['leave'] }}%</h2>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Children's Attendance Table -->
                <div class="card">
                    <div class="card-header bg-light">
                        <h5 class="card-title mb-0">
                            <i class="fa fa-list mr-2"></i>Children's Attendance for {{ $selectedDate->format('d M, Y') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        @if(count($children) > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Child Name</th>
                                        <th>Class</th>
                                        <th>Status</th>
                                        <th>Remarks</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($children as $index => $child)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $child->name }}</td>
                                        <td>{{ $child->classes->first()->name ?? 'Not Assigned' }}</td>
                                        <td>
                                            @if($child->attendance)
                                            <span class="badge badge-{{ $child->attendance->status === 'present' ? 'success' : ($child->attendance->status === 'absent' ? 'danger' : ($child->attendance->status === 'late' ? 'warning' : 'info')) }}">
                                                {{ ucfirst($child->attendance->status) }}
                                            </span>
                                            @else
                                            <span class="badge badge-secondary">Not Marked</span>
                                            @endif
                                        </td>
                                        <td>{{ $child->attendance->remarks ?? '-' }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <div class="alert alert-info">
                            <i class="fa fa-info-circle mr-2"></i>No children found.
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection