@extends('backend.layouts.app')

@section('title', 'Activity Details')

@section('content')
<div class="row pt-3">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">Activity Details</h4>
                <div>
                    <a href="{{ route('activities.index') }}" class="btn btn-primary btn-sm">
                        <i class="fa fa-arrow-left mr-1"></i> Back to Activities
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="activity-details">
                            <div class="card mb-4">
                                <div class="card-header bg-light">
                                    <h5 class="card-title mb-0">
                                        <i class="fa fa-info-circle mr-2"></i>Basic Information
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <table class="table table-bordered">
                                                <tr>
                                                    <th width="30%">Activity ID</th>
                                                    <td>{{ $activity->id }}</td>
                                                </tr>
                                                <tr>
                                                    <th>User</th>
                                                    <td>{{ $activity->user ? $activity->user->name : 'System' }}</td>
                                                </tr>
                                                <tr>
                                                    <th>User Role</th>
                                                    <td>
                                                        @if($activity->user)
                                                        @if($activity->user->role == 1)
                                                        Admin
                                                        @elseif($activity->user->role == 2)
                                                        Teacher
                                                        @elseif($activity->user->role == 3)
                                                        Parent
                                                        @elseif($activity->user->role == 4)
                                                        Student
                                                        @else
                                                        Unknown
                                                        @endif
                                                        @else
                                                        System
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>IP Address</th>
                                                    <td>{{ $activity->ip_address }}</td>
                                                </tr>
                                            </table>
                                        </div>
                                        <div class="col-md-6">
                                            <table class="table table-bordered">
                                                <tr>
                                                    <th width="30%">Date</th>
                                                    <td>{{ $activity->created_at->format('F d, Y') }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Time</th>
                                                    <td>{{ $activity->created_at->format('h:i:s A') }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Activity Type</th>
                                                    <td>
                                                        @if(strpos($activity->description, 'fee') !== false ||
                                                        strpos($activity->description, 'Fee') !== false)
                                                        <span class="badge badge-info">Fee Related</span>
                                                        @elseif(strpos($activity->description, 'student') !== false ||
                                                        strpos($activity->description, 'Student') !== false)
                                                        <span class="badge badge-primary">Student Related</span>
                                                        @elseif(strpos($activity->description, 'teacher') !== false ||
                                                        strpos($activity->description, 'Teacher') !== false)
                                                        <span class="badge badge-success">Teacher Related</span>
                                                        @elseif(strpos($activity->description, 'class') !== false ||
                                                        strpos($activity->description, 'Class') !== false)
                                                        <span class="badge badge-warning">Class Related</span>
                                                        @elseif(strpos($activity->description, 'parent') !== false ||
                                                        strpos($activity->description, 'Parent') !== false)
                                                        <span class="badge badge-secondary">Parent Related</span>
                                                        @elseif(strpos($activity->description, 'fine') !== false ||
                                                        strpos($activity->description, 'Fine') !== false)
                                                        <span class="badge badge-danger">Fine Related</span>
                                                        @elseif(strpos($activity->description, 'report') !== false ||
                                                        strpos($activity->description, 'Report') !== false)
                                                        <span class="badge badge-dark">Report Related</span>
                                                        @else
                                                        <span class="badge badge-light">Other</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>Time Ago</th>
                                                    <td>{{ $activity->created_at->diffForHumans() }}</td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-header bg-light">
                                    <h5 class="card-title mb-0">
                                        <i class="fa fa-file-text mr-2"></i>Activity Description
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="alert alert-info">
                                        <p class="mb-0">{{ $activity->description }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection