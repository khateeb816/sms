@extends('backend.layouts.app')

@section('title', 'Teacher Details')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Teacher Details</h3>
                <div class="card-action">
                    <a href="{{ route('teachers.index') }}" class="btn btn-secondary">Back to List</a>
                    <a href="{{ route('teachers.edit', $teacher->id) }}" class="btn btn-primary">Edit Teacher</a>
                </div>
            </div>
            <div class="card-body">
                <!-- Basic Information Card -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h4 class="card-title">Basic Information</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 text-center mb-4">
                                <div class="profile-image-container mb-3">
                                    <img src="{{ $teacher->image ? asset('storage/profile_pictures/'.$teacher->image) : 'https://ui-avatars.com/api/?name='.urlencode($teacher->name).'&background=random&color=fff&size=200' }}"
                                        alt="{{ $teacher->name }}" class="img-fluid rounded-circle"
                                        style="width: 200px; height: 200px; object-fit: cover;">
                                    <div class="edit-icon" data-toggle="tooltip" title="Change Profile Picture">
                                        <label for="profile_picture">
                                            <i class="fas fa-pencil-alt"></i>
                                        </label>
                                    </div>
                                </div>

                                <form action="{{ route('teachers.update-picture', $teacher->id) }}" method="POST"
                                    enctype="multipart/form-data" id="profile-picture-form">
                                    @csrf
                                    @method('PUT')
                                    <input type="file" name="profile_picture" id="profile_picture" class="d-none"
                                        onchange="document.getElementById('profile-picture-form').submit()">
                                </form>

                                <h4 class="mt-2">{{ $teacher->name }}</h4>
                                @if($teacher->status == 'active')
                                <span class="badge badge-success">Active</span>
                                @else
                                <span class="badge badge-warning">Inactive</span>
                                @endif
                            </div>

                            <div class="col-md-8">
                                <div class="row mb-3">
                                    <div class="col-md-4 font-weight-bold">Email:</div>
                                    <div class="col-md-8">{{ $teacher->email }}</div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-4 font-weight-bold">Phone:</div>
                                    <div class="col-md-8">{{ $teacher->phone ?? 'Not provided' }}</div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-4 font-weight-bold">Qualification:</div>
                                    <div class="col-md-8">{{ $teacher->teacherDetail->qualification ?? 'Not provided' }}
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-4 font-weight-bold">Specialization:</div>
                                    <div class="col-md-8">{{ $teacher->teacherDetail->specialization ?? 'Not provided'
                                        }}</div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-4 font-weight-bold">Address:</div>
                                    <div class="col-md-8">{{ $teacher->address ?? 'Not provided' }}</div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-4 font-weight-bold">Joined:</div>
                                    <div class="col-md-8">{{ $teacher->created_at->format('F d, Y') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Education & Certification Card -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h4 class="card-title">Education & Certification</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label font-weight-bold">Education Level:</label>
                                    <div class="col-sm-8 pt-2">
                                        {{ $teacher->teacherDetail->education_level ?? 'Not provided' }}
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label font-weight-bold">University:</label>
                                    <div class="col-sm-8 pt-2">
                                        {{ $teacher->teacherDetail->university ?? 'Not provided' }}
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label font-weight-bold">Degree:</label>
                                    <div class="col-sm-8 pt-2">
                                        {{ $teacher->teacherDetail->degree ?? 'Not provided' }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label font-weight-bold">Major:</label>
                                    <div class="col-sm-8 pt-2">
                                        {{ $teacher->teacherDetail->major ?? 'Not provided' }}
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label font-weight-bold">Graduation Year:</label>
                                    <div class="col-sm-8 pt-2">
                                        {{ $teacher->teacherDetail->graduation_year ?? 'Not provided' }}
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label font-weight-bold">Certification:</label>
                                    <div class="col-sm-8 pt-2">
                                        {{ $teacher->teacherDetail->certification ?? 'Not provided' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label class="font-weight-bold">Teaching Experience:</label>
                                    <div class="pt-2">
                                        {!! nl2br(e($teacher->teacherDetail->teaching_experience ?? 'Not provided')) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label class="font-weight-bold">Biography:</label>
                                    <div class="pt-2">
                                        {!! nl2br(e($teacher->teacherDetail->biography ?? 'Not provided')) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Emergency Contact Card -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h4 class="card-title">Emergency Contact</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label font-weight-bold">Contact Name:</label>
                                    <div class="col-sm-8 pt-2">
                                        {{ $teacher->teacherDetail->emergency_contact_name ?? 'Not provided' }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label font-weight-bold">Contact Phone:</label>
                                    <div class="col-sm-8 pt-2">
                                        {{ $teacher->teacherDetail->emergency_contact_phone ?? 'Not provided' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label font-weight-bold">Relationship:</label>
                                    <div class="col-sm-8 pt-2">
                                        {{ $teacher->teacherDetail->emergency_contact_relationship ?? 'Not provided' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Financial Information Card -->
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Financial Information</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label font-weight-bold">Bank Name:</label>
                                    <div class="col-sm-8 pt-2">
                                        {{ $teacher->teacherDetail->bank_name ?? 'Not provided' }}
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label font-weight-bold">Account Number:</label>
                                    <div class="col-sm-8 pt-2">
                                        {{ $teacher->teacherDetail->bank_account_number ?? 'Not provided' }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label font-weight-bold">Branch:</label>
                                    <div class="col-sm-8 pt-2">
                                        {{ $teacher->teacherDetail->bank_branch ?? 'Not provided' }}
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

@push('styles')
<style>
    .profile-image-container {
        position: relative;
        display: inline-block;
    }

    .edit-icon {
        position: absolute;
        bottom: 10px;
        right: 10px;
        background-color: #007bff;
        color: white;
        border-radius: 50%;
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s;
    }

    .edit-icon:hover {
        background-color: #0069d9;
    }

    .card {
        background-color: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .card-header {
        background-color: rgba(255, 255, 255, 0.05);
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .card-title {
        color: #fff;
        margin-bottom: 0;
    }

    .form-group {
        margin-bottom: 1rem;
    }

    .form-group label {
        margin-bottom: 0.5rem;
        color: rgba(255, 255, 255, 0.8);
    }

    .form-group .pt-2 {
        color: #fff;
    }

    .badge {
        padding: 5px 10px;
        font-size: 12px;
    }
</style>
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
        // Initialize tooltips
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>
@endpush
@endsection