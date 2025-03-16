@extends('backend.layouts.app')

@section('title', 'View Parent')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <div class="text-center">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($parent->name) }}&background=random"
                            class="rounded-circle img-thumbnail" alt="Profile Image"
                            style="width: 150px; height: 150px;">
                        <h4 class="mt-3">{{ $parent->name }}</h4>
                        <p class="text-muted">Parent</p>
                    </div>
                    <hr>
                    <div class="profile-details mt-4">
                        <h5 class="mb-3">Contact Information</h5>
                        <div class="row mb-2">
                            <div class="col-sm-4 text-muted">Email:</div>
                            <div class="col-sm-8">{{ $parent->email }}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-sm-4 text-muted">Phone:</div>
                            <div class="col-sm-8">{{ $parent->phone ?? 'Not provided' }}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-sm-4 text-muted">Address:</div>
                            <div class="col-sm-8">{{ $parent->address ?? 'Not provided' }}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-sm-4 text-muted">Status:</div>
                            <div class="col-sm-8">
                                @if($parent->status == 'active')
                                <span class="badge badge-success">Active</span>
                                @else
                                <span class="badge badge-warning">Inactive</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Children</h3>
                    <a href="{{ url('/dash/parents/'.$parent->id.'/add-child') }}" class="btn btn-primary btn-sm">
                        <i class="icon-plus"></i> Add Child
                    </a>
                </div>
                <div class="card-body">
                    @if(count($children) > 0)
                    <div class="row">
                        @foreach($children as $child)
                        <div class="col-md-6 mb-4">
                            <div class="card bg-dark h-100">
                                <div class="card-body">
                                    <div class="d-flex">
                                        <div class="mr-3">
                                            <img src="{{ $child->image ? asset('storage/profile_pictures/'.$child->image) : 'https://ui-avatars.com/api/?name='.urlencode($child->name).'&background=random' }}"
                                                class="rounded-circle img-thumbnail" alt="Student Image"
                                                style="width: 80px; height: 80px; object-fit: cover;">
                                        </div>
                                        <div class="flex-grow-1">
                                            <h5 class="card-title">{{ $child->name }}</h5>
                                            <div class="mb-2">
                                                <span class="badge badge-primary mr-2">Class: {{ $child->class ?? 'Not
                                                    assigned' }}</span>
                                                <span class="badge badge-info">Roll: {{ $child->roll_number ?? 'Not
                                                    assigned' }}</span>
                                            </div>
                                            <p class="mb-1 small"><i class="zmdi zmdi-phone mr-2"></i>{{ $child->phone
                                                ?? 'Not provided' }}</p>
                                            <p class="mb-1 small text-truncate"><i class="zmdi zmdi-pin mr-2"></i>{{
                                                $child->address ?? 'Not provided' }}</p>

                                            <div class="mt-3 d-flex">
                                                <a href="{{ url('/dash/students/'.$child->id) }}"
                                                    class="btn btn-info btn-sm mr-1">View</a>
                                                <a href="{{ url('/dash/students/'.$child->id.'/edit') }}"
                                                    class="btn btn-primary btn-sm mr-1">Edit</a>
                                                <a href="#" class="btn btn-danger btn-sm"
                                                    onclick="event.preventDefault(); if(confirm('Are you sure you want to remove this child from this parent?')) { document.getElementById('remove-child-form-{{ $child->id }}').submit(); }">Remove</a>
                                                <form id="remove-child-form-{{ $child->id }}"
                                                    action="{{ url('/dash/parents/'.$parent->id.'/remove-child/'.$child->id) }}"
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
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-5">
                        <i class="zmdi zmdi-mood-bad" style="font-size: 48px;"></i>
                        <p class="mt-3">No children records found</p>
                        <a href="{{ url('/dash/parents/'.$parent->id.'/add-child') }}"
                            class="btn btn-primary btn-sm mt-2">
                            <i class="icon-plus"></i> Add Child
                        </a>
                    </div>
                    @endif
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
                                <p class="text-muted">{{ $parent->created_at->format('F d, Y h:i A') }}</p>
                            </div>
                        </li>
                        @if($parent->created_at != $parent->updated_at)
                        <li class="timeline-item">
                            <span class="timeline-point"></span>
                            <div class="timeline-content">
                                <h5 class="mb-1">Profile Updated</h5>
                                <p class="text-muted">{{ $parent->updated_at->format('F d, Y h:i A') }}</p>
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
            <a href="{{ url('/dash/parents') }}" class="btn btn-light">Back to List</a>
            <a href="{{ url('/dash/parents/'.$parent->id.'/edit') }}" class="btn btn-primary">Edit Parent</a>
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
</style>
@endsection