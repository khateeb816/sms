@extends('backend.layouts.app')

@section('title', 'Teacher Attendance')

@section('content')
<div class="row pt-3">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">Teacher Attendance</h4>
                <div>
                    <a href="{{ route('attendance.reports', ['type' => 'teacher']) }}" class="btn btn-info btn-sm">
                        <i class="fa fa-chart-bar mr-1"></i> Attendance Reports
                    </a>
                </div>
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
                                <form action="{{ route('attendance.teachers.index') }}" method="GET" id="filterForm">
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
                                                    <i class="fa fa-search mr-1"></i> Load Teachers
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Attendance Form -->
                <div class="card">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="fa fa-clipboard-check mr-2"></i>Mark Attendance for {{ $selectedDate->format('d M,
                            Y') }}
                        </h5>

                        @php
                        $isToday = $selectedDate->isToday();
                        $hasAttendance = $teachers->filter(function($teacher) {
                        return $teacher->attendance !== null;
                        })->count() > 0;
                        @endphp

                        @if($hasAttendance)
                        <div>
                            <span class="badge badge-success p-2">
                                <i class="fa fa-check mr-1"></i> Attendance Already Marked
                            </span>
                        </div>
                        @endif
                    </div>
                    <div class="card-body">
                        @if(count($teachers) > 0)
                        <form action="{{ route('attendance.teachers.mark') }}" method="POST">
                            @csrf
                            <input type="hidden" name="date" value="{{ $selectedDate->format('Y-m-d') }}">

                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th width="5%">#</th>
                                            <th width="25%">Teacher Name</th>
                                            <th width="20%">Status</th>
                                            <th width="40%">Remarks</th>
                                            <th width="10%">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($teachers as $index => $teacher)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $teacher->name }}</td>
                                            <td>
                                                <select class="form-control attendance-status-select"
                                                    name="attendance[{{ $index }}][status]" {{ $teacher->attendance ?
                                                    'disabled' : '' }}>
                                                    <option value="present" {{ $teacher->attendance &&
                                                        $teacher->attendance->status == 'present' ? 'selected' : ''
                                                        }}>Present</option>
                                                    <option value="absent" {{ $teacher->attendance &&
                                                        $teacher->attendance->status == 'absent' ? 'selected' : ''
                                                        }}>Absent</option>
                                                    <option value="late" {{ $teacher->attendance &&
                                                        $teacher->attendance->status == 'late' ? 'selected' : '' }}>Late
                                                    </option>
                                                    <option value="leave" {{ $teacher->attendance &&
                                                        $teacher->attendance->status == 'leave' ? 'selected' : ''
                                                        }}>Leave</option>
                                                </select>
                                                <input type="hidden" name="attendance[{{ $index }}][user_id]"
                                                    value="{{ $teacher->id }}">
                                            </td>
                                            <td>
                                                <input type="text" class="form-control"
                                                    name="attendance[{{ $index }}][remarks]"
                                                    value="{{ $teacher->attendance ? $teacher->attendance->remarks : '' }}"
                                                    {{ $teacher->attendance ? 'disabled' : '' }}>
                                            </td>
                                            <td class="text-center">
                                                @if($teacher->attendance)
                                                <a href="{{ route('attendance.edit', $teacher->attendance->id) }}"
                                                    class="btn btn-sm btn-primary">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-danger delete-attendance"
                                                    data-id="{{ $teacher->attendance->id }}"
                                                    data-teacher="{{ $teacher->name }}">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                                @else
                                                <span class=" ">Not marked</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            @if(!$hasAttendance)
                            <div class="form-group text-center mt-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-save mr-1"></i> Save Attendance
                                </button>
                            </div>
                            @endif
                        </form>

                        <!-- Delete Forms - Moved outside the main form -->
                        @foreach($teachers as $teacher)
                        @if($teacher->attendance)
                        <form id="delete-form-{{ $teacher->attendance->id }}"
                            action="{{ route('attendance.delete', $teacher->attendance->id) }}" method="POST"
                            style="display: none;">
                            @csrf
                            @method('DELETE')
                        </form>
                        @endif
                        @endforeach

                        @else
                        <div class="alert alert-info">
                            <i class="fa fa-info-circle mr-1"></i> No teachers found.
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-submit form when date changes
        document.getElementById('date').addEventListener('change', function() {
            document.getElementById('filterForm').submit();
        });

        // Handle delete button clicks
        document.querySelectorAll('.delete-attendance').forEach(function(button) {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                var id = this.getAttribute('data-id');
                var teacherName = this.getAttribute('data-teacher');
                var form = document.getElementById('delete-form-' + id);
                
                if (!form) {
                    console.error('Could not find form with ID: delete-form-' + id);
                    return;
                }
                
                if (confirm('Are you sure you want to delete attendance record for ' + teacherName + '?')) {
                    form.submit();
                }
            });
        });
        
        // Add status indicators to the attendance status dropdowns
        document.querySelectorAll('.attendance-status-select').forEach(function(select) {
            var selectedValue = select.value;
            
            // Add status indicators to each option
            select.querySelectorAll('option').forEach(function(option) {
                var value = option.value;
                var text = option.text;
                var colorClass = '';
                var icon = '';
                
                switch(value) {
                    case 'present':
                        colorClass = 'text-success';
                        icon = '<i class="fa fa-check-circle"></i> ';
                        break;
                    case 'absent':
                        colorClass = 'text-danger';
                        icon = '<i class="fa fa-times-circle"></i> ';
                        break;
                    case 'late':
                        colorClass = 'text-warning';
                        icon = '<i class="fa fa-clock"></i> ';
                        break;
                    case 'leave':
                        colorClass = 'text-info';
                        icon = '<i class="fa fa-calendar-minus"></i> ';
                        break;
                }
                
                // We can't add HTML to options directly, but we can add a status indicator before the select
                if (value === selectedValue && !select.disabled) {
                    var statusIndicator = document.createElement('span');
                    statusIndicator.className = 'status-indicator status-' + value;
                    select.parentNode.insertBefore(statusIndicator, select);
                }
            });
        });
    });
</script>

@endsection

@section('header')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection


@push('styles')
<style>
    /* Custom styling for attendance status dropdown */
    .attendance-status-select option[value="present"] {
        background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="%2328a745" d="M256 8C119.033 8 8 119.033 8 256s111.033 248 248 248 248-111.033 248-248S392.967 8 256 8zm0 48c110.532 0 200 89.451 200 200 0 110.532-89.451 200-200 200-110.532 0-200-89.451-200-200 0-110.532 89.451-200 200-200m140.204 130.267l-22.536-22.718c-4.667-4.705-12.265-4.736-16.97-.068L215.346 303.697l-59.792-60.277c-4.667-4.705-12.265-4.736-16.97-.069l-22.719 22.536c-4.705 4.667-4.736 12.265-.068 16.971l90.781 91.516c4.667 4.705 12.265 4.736 16.97.068l172.589-171.204c4.704-4.668 4.734-12.266.067-16.971z"/></svg>');
        background-repeat: no-repeat;
        background-position: 5px center;
        background-size: 16px;
        padding-left: 25px;
    }

    .attendance-status-select option[value="absent"] {
        background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="%23dc3545" d="M256 8C119 8 8 119 8 256s111 248 248 248 248-111 248-248S393 8 256 8zm0 448c-110.5 0-200-89.5-200-200S145.5 56 256 56s200 89.5 200 200-89.5 200-200 200zm101.8-262.2L295.6 256l62.2 62.2c4.7 4.7 4.7 12.3 0 17l-22.6 22.6c-4.7 4.7-12.3 4.7-17 0L256 295.6l-62.2 62.2c-4.7 4.7-12.3 4.7-17 0l-22.6-22.6c-4.7-4.7-4.7-12.3 0-17l62.2-62.2-62.2-62.2c-4.7-4.7-4.7-12.3 0-17l22.6-22.6c4.7-4.7 12.3-4.7 17 0l62.2 62.2 62.2-62.2c4.7-4.7 12.3-4.7 17 0l22.6 22.6c4.7 4.7 4.7 12.3 0 17z"/></svg>');
        background-repeat: no-repeat;
        background-position: 5px center;
        background-size: 16px;
        padding-left: 25px;
    }

    .attendance-status-select option[value="late"] {
        background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="%23ffc107" d="M256 8C119 8 8 119 8 256s111 248 248 248 248-111 248-248S393 8 256 8zm0 448c-110.5 0-200-89.5-200-200S145.5 56 256 56s200 89.5 200 200-89.5 200-200 200zm61.8-104.4l-84.9-61.7c-3.1-2.3-4.9-5.9-4.9-9.7V116c0-6.6 5.4-12 12-12h32c6.6 0 12 5.4 12 12v141.7l66.8 48.6c5.4 3.9 6.5 11.4 2.6 16.8L334.6 349c-3.9 5.3-11.4 6.5-16.8 2.6z"/></svg>');
        background-repeat: no-repeat;
        background-position: 5px center;
        background-size: 16px;
        padding-left: 25px;
    }

    .attendance-status-select option[value="leave"] {
        background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="%2317a2b8" d="M400 64h-48V12c0-6.6-5.4-12-12-12h-40c-6.6 0-12 5.4-12 12v52H160V12c0-6.6-5.4-12-12-12h-40c-6.6 0-12 5.4-12 12v52H48C21.5 64 0 85.5 0 112v352c0 26.5 21.5 48 48 48h352c26.5 0 48-21.5 48-48V112c0-26.5-21.5-48-48-48zm-6 400H54c-3.3 0-6-2.7-6-6V160h352v298c0 3.3-2.7 6-6 6z"/></svg>');
        background-repeat: no-repeat;
        background-position: 5px center;
        background-size: 16px;
        padding-left: 25px;
    }

    /* Custom styling for status indicators */
    .status-indicator {
        display: inline-block;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        margin-right: 5px;
    }

    .status-present {
        background-color: #28a745;
    }

    .status-absent {
        background-color: #dc3545;
    }

    .status-late {
        background-color: #ffc107;
    }

    .status-leave {
        background-color: #17a2b8;
    }
</style>
@endpush
