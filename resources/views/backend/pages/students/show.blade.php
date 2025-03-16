@extends('backend.layouts.app')

@section('title', 'View Student')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <div class="text-center">
                        <div class="position-relative d-inline-block">
                            <img src="{{ $student->image ? asset('storage/profile_pictures/'.$student->image) : 'https://ui-avatars.com/api/?name='.urlencode($student->name).'&background=random' }}"
                                class="rounded-circle img-thumbnail" alt="Profile Image"
                                style="width: 150px; height: 150px; object-fit: cover;" id="profile_image">
                            <div class="profile-edit-icon">
                                <a href="#"
                                    onclick="document.getElementById('profile_picture_upload').click(); return false;"
                                    data-toggle="tooltip" title="Change profile picture">
                                    <i class="zmdi zmdi-edit"></i>
                                </a>
                            </div>
                        </div>
                        <form id="profile_picture_form"
                            action="{{ url('/dash/students/'.$student->id.'/update-picture') }}" method="POST"
                            enctype="multipart/form-data" style="display: none;">
                            @csrf
                            @method('PUT')
                            <input type="file" name="profile_picture" id="profile_picture_upload" accept="image/*"
                                onchange="submitProfilePicture()">
                        </form>
                        <h4 class="mt-3">{{ $student->name }}</h4>
                        <p class="text-muted">Student</p>
                        <div class="d-flex justify-content-center">
                            <span class="badge badge-primary mr-2">Class: {{ isset($student->classes) &&
                                count($student->classes) > 0 ? $student->classes->first()->name : 'Not assigned'
                                }}</span>
                            <span class="badge badge-info">Roll: {{ $student->roll_number ?? 'Not assigned' }}</span>
                        </div>
                    </div>
                    <hr>
                    <div class="profile-details mt-4">
                        <h5 class="mb-3">Contact Information</h5>
                        <div class="row mb-2">
                            <div class="col-sm-4 text-muted">Phone:</div>
                            <div class="col-sm-8">{{ $student->phone ?? 'Not provided' }}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-sm-4 text-muted">Email:</div>
                            <div class="col-sm-8">
                                <span class="badge badge-secondary">{{ $student->email }}</span>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-sm-4 text-muted">Address:</div>
                            <div class="col-sm-8">{{ $student->address ?? 'Not provided' }}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-sm-4 text-muted">Status:</div>
                            <div class="col-sm-8">
                                @if($student->status == 'active')
                                <span class="badge badge-success">Active</span>
                                @else
                                <span class="badge badge-warning">Inactive</span>
                                @endif
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-sm-4 text-muted">Password:</div>
                            <div class="col-sm-8">
                                <span class="badge badge-secondary">Default (123456)</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Parent Information</h3>
                </div>
                <div class="card-body">
                    @if($parent)
                    <div class="row">
                        <div class="col-md-2">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($parent->name) }}&background=random"
                                class="rounded-circle img-thumbnail" alt="Parent Image"
                                style="width: 80px; height: 80px;">
                        </div>
                        <div class="col-md-10">
                            <h5>{{ $parent->name }}</h5>
                            <p class="mb-1"><i class="zmdi zmdi-email mr-2"></i> {{ $parent->email }}</p>
                            <p class="mb-1"><i class="zmdi zmdi-phone mr-2"></i> {{ $parent->phone ?? 'Not provided' }}
                            </p>
                            <a href="{{ url('/dash/parents/'.$parent->id) }}" class="btn btn-info btn-sm mt-2">View
                                Parent Profile</a>
                        </div>
                    </div>
                    @else
                    <div class="text-center py-3">
                        <p class="mb-0">No parent assigned to this student.</p>
                        <a href="{{ url('/dash/students/'.$student->id.'/edit') }}"
                            class="btn btn-primary btn-sm mt-2">Assign
                            Parent</a>
                    </div>
                    @endif
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header">
                    <h3 class="card-title">Academic Information</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card bg-dark">
                                <div class="card-body">
                                    <h5 class="card-title">Class</h5>
                                    <p class="card-text">
                                        @if(isset($student->classes) && count($student->classes) > 0)
                                        {{ $student->classes->first()->name }}
                                        @if($student->classes->count() > 1)
                                        <span class="badge badge-info ml-2">+{{ $student->classes->count() - 1 }}
                                            more</span>
                                        @endif
                                        @else
                                        Not assigned
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-dark">
                                <div class="card-body">
                                    <h5 class="card-title">Roll Number</h5>
                                    <p class="card-text">{{ $student->roll_number ?? 'Not assigned' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="text-center mt-3">
                        <p class="text-muted">More academic information will be available here.</p>
                    </div>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header">
                    <h3 class="card-title">Recent Activities</h3>
                </div>
                <div class="card-body">
                    <ul class="timeline">
                        <li class="timeline-item">
                            <span class="timeline-point"></span>
                            <div class="timeline-content">
                                <h5 class="mb-1">Account Created</h5>
                                <p class="text-muted">{{ $student->created_at->format('F d, Y h:i A') }}</p>
                            </div>
                        </li>
                        @if($student->created_at != $student->updated_at)
                        <li class="timeline-item">
                            <span class="timeline-point"></span>
                            <div class="timeline-content">
                                <h5 class="mb-1">Profile Updated</h5>
                                <p class="text-muted">{{ $student->updated_at->format('F d, Y h:i A') }}</p>
                            </div>
                        </li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-3">
        <div class="col-12">
            <a href="{{ url('/dash/students') }}" class="btn btn-light">Back to List</a>
            <a href="{{ url('/dash/students/'.$student->id.'/edit') }}" class="btn btn-primary">Edit Student</a>
        </div>
    </div>
</div>

<style>
    .timeline {
        position: relative;
        padding-left: 30px;
        list-style: none;
    }

    .timeline-item {
        position: relative;
        padding-bottom: 20px;
    }

    .timeline-point {
        position: absolute;
        left: -30px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background-color: #007bff;
        display: block;
    }

    .timeline-point:before {
        content: '';
        position: absolute;
        left: 5px;
        top: 12px;
        height: calc(100% + 8px);
        width: 2px;
        background-color: rgba(255, 255, 255, 0.2);
    }

    .timeline-item:last-child .timeline-point:before {
        display: none;
    }

    /* Profile picture edit icon styles */
    .position-relative {
        position: relative;
    }

    .profile-edit-icon {
        position: absolute;
        bottom: 0;
        right: 10px;
        background-color: rgba(0, 123, 255, 0.8);
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s;
    }

    .profile-edit-icon:hover {
        background-color: rgba(0, 123, 255, 1);
    }

    .profile-edit-icon a {
        color: white;
        font-size: 18px;
    }
</style>

<script>
    function submitProfilePicture() {
        document.getElementById('profile_picture_form').submit();
    }
    
    // Initialize tooltips
    $(function () {
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>
@endsection