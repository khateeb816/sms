@extends('backend.layouts.app')

@section('title', 'Edit Attendance')

@section('content')
<div class="row pt-3">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">Edit Attendance</h4>
                <div>
                    @if($attendance->attendee_type == 'student')
                    <a href="{{ route('attendance.students.index', ['date' => $attendance->date->format('Y-m-d')]) }}"
                        class="btn btn-primary btn-sm">
                        <i class="fa fa-arrow-left mr-1"></i> Back to Student Attendance
                    </a>
                    @else
                    <a href="{{ route('attendance.teachers.index', ['date' => $attendance->date->format('Y-m-d')]) }}"
                        class="btn btn-primary btn-sm">
                        <i class="fa fa-arrow-left mr-1"></i> Back to Teacher Attendance
                    </a>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 offset-md-3">
                        <div class="card">
                            <div class="card-header bg-light">
                                <h5 class="card-title mb-0">
                                    <i class="fa fa-edit mr-2"></i>Edit Attendance for {{ $user->name }}
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-4">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>Name:</strong> {{ $user->name }}</p>
                                            <p><strong>Date:</strong> {{ $attendance->date->format('d M, Y') }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Type:</strong> {{ ucfirst($attendance->attendee_type) }}</p>
                                            @if($attendance->attendee_type == 'student' && isset($userClass))
                                            <p><strong>Class:</strong> {{ $userClass->name ?? 'N/A' }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <form action="{{ route('attendance.update', $attendance->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')

                                    <div class="form-group">
                                        <label for="status">Attendance Status</label>
                                        <select class="form-control @error('status') is-invalid @enderror" id="status"
                                            name="status" required>
                                            <option value="present" {{ $attendance->status == 'present' ? 'selected' :
                                                '' }}>Present</option>
                                            <option value="absent" {{ $attendance->status == 'absent' ? 'selected' : ''
                                                }}>Absent</option>
                                            <option value="late" {{ $attendance->status == 'late' ? 'selected' : ''
                                                }}>Late</option>
                                            <option value="leave" {{ $attendance->status == 'leave' ? 'selected' : ''
                                                }}>Leave</option>
                                        </select>
                                        @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="remarks">Remarks</label>
                                        <textarea class="form-control @error('remarks') is-invalid @enderror"
                                            id="remarks" name="remarks" rows="3">{{ $attendance->remarks }}</textarea>
                                        @error('remarks')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group text-center mt-4">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fa fa-save mr-1"></i> Update Attendance
                                        </button>

                                        <a href="#" class="btn btn-danger ml-2"
                                            onclick="event.preventDefault(); if(confirm('Are you sure you want to delete this attendance record?')) document.getElementById('delete-form').submit();">
                                            <i class="fa fa-trash mr-1"></i> Delete Attendance
                                        </a>
                                    </div>
                                </form>

                                <form id="delete-form" action="{{ route('attendance.delete', $attendance->id) }}"
                                    method="POST" style="display: none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection