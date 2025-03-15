@extends('backend.layouts.app')

@section('title', 'Edit Teacher')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Edit Teacher</h3>
                <div class="card-action">
                    <a href="{{ route('teachers.index') }}" class="btn btn-secondary">Back to List</a>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('teachers.update', $teacher->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

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
                                    <input type="file" name="profile_picture" id="profile_picture" class="d-none">
                                </div>

                                <div class="col-md-8">
                                    <div class="form-group row">
                                        <label class="col-sm-4 col-form-label">Name:</label>
                                        <div class="col-sm-8">
                                            <input type="text" name="name" class="form-control"
                                                value="{{ $teacher->name }}" required>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-4 col-form-label">Email:</label>
                                        <div class="col-sm-8">
                                            <input type="email" name="email" class="form-control"
                                                value="{{ $teacher->email }}" required>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-4 col-form-label">Phone:</label>
                                        <div class="col-sm-8">
                                            <input type="text" name="phone" class="form-control"
                                                value="{{ $teacher->phone }}">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-4 col-form-label">Qualification:</label>
                                        <div class="col-sm-8">
                                            <input type="text" name="qualification" class="form-control"
                                                value="{{ $teacher->teacherDetail ? $teacher->teacherDetail->qualification : '' }}">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-4 col-form-label">Specialization:</label>
                                        <div class="col-sm-8">
                                            <input type="text" name="specialization" class="form-control"
                                                value="{{ $teacher->teacherDetail ? $teacher->teacherDetail->specialization : '' }}">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-4 col-form-label">Address:</label>
                                        <div class="col-sm-8">
                                            <textarea name="address" class="form-control"
                                                rows="3">{{ $teacher->address }}</textarea>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-4 col-form-label">Status:</label>
                                        <div class="col-sm-8">
                                            <select name="status" class="form-control">
                                                <option value="active" {{ $teacher->status == 'active' ? 'selected' : ''
                                                    }}>Active</option>
                                                <option value="inactive" {{ $teacher->status == 'inactive' ? 'selected'
                                                    : '' }}>Inactive</option>
                                            </select>
                                        </div>
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
                                        <label class="col-sm-4 col-form-label">Education Level:</label>
                                        <div class="col-sm-8">
                                            <input type="text" name="education_level" class="form-control"
                                                value="{{ $teacher->teacherDetail ? $teacher->teacherDetail->education_level : '' }}">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-4 col-form-label">University:</label>
                                        <div class="col-sm-8">
                                            <input type="text" name="university" class="form-control"
                                                value="{{ $teacher->teacherDetail ? $teacher->teacherDetail->university : '' }}">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-4 col-form-label">Degree:</label>
                                        <div class="col-sm-8">
                                            <input type="text" name="degree" class="form-control"
                                                value="{{ $teacher->teacherDetail ? $teacher->teacherDetail->degree : '' }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-4 col-form-label">Major:</label>
                                        <div class="col-sm-8">
                                            <input type="text" name="major" class="form-control"
                                                value="{{ $teacher->teacherDetail ? $teacher->teacherDetail->major : '' }}">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-4 col-form-label">Graduation Year:</label>
                                        <div class="col-sm-8">
                                            <input type="text" name="graduation_year" class="form-control"
                                                value="{{ $teacher->teacherDetail ? $teacher->teacherDetail->graduation_year : '' }}">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-4 col-form-label">Certification:</label>
                                        <div class="col-sm-8">
                                            <input type="text" name="certification" class="form-control"
                                                value="{{ $teacher->teacherDetail ? $teacher->teacherDetail->certification : '' }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label>Teaching Experience:</label>
                                        <textarea name="teaching_experience" class="form-control"
                                            rows="4">{{ $teacher->teacherDetail ? $teacher->teacherDetail->teaching_experience : '' }}</textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label>Biography:</label>
                                        <textarea name="biography" class="form-control"
                                            rows="4">{{ $teacher->teacherDetail ? $teacher->teacherDetail->biography : '' }}</textarea>
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
                                        <label class="col-sm-4 col-form-label">Contact Name:</label>
                                        <div class="col-sm-8">
                                            <input type="text" name="emergency_contact_name" class="form-control"
                                                value="{{ $teacher->teacherDetail ? $teacher->teacherDetail->emergency_contact_name : '' }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-4 col-form-label">Contact Phone:</label>
                                        <div class="col-sm-8">
                                            <input type="text" name="emergency_contact_phone" class="form-control"
                                                value="{{ $teacher->teacherDetail ? $teacher->teacherDetail->emergency_contact_phone : '' }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-4 col-form-label">Relationship:</label>
                                        <div class="col-sm-8">
                                            <input type="text" name="emergency_contact_relationship"
                                                class="form-control"
                                                value="{{ $teacher->teacherDetail ? $teacher->teacherDetail->emergency_contact_relationship : '' }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Financial Information Card -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h4 class="card-title">Financial Information</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-4 col-form-label">Bank Name:</label>
                                        <div class="col-sm-8">
                                            <input type="text" name="bank_name" class="form-control"
                                                value="{{ $teacher->teacherDetail ? $teacher->teacherDetail->bank_name : '' }}">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-4 col-form-label">Account Number:</label>
                                        <div class="col-sm-8">
                                            <input type="text" name="bank_account_number" class="form-control"
                                                value="{{ $teacher->teacherDetail ? $teacher->teacherDetail->bank_account_number : '' }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-4 col-form-label">Branch:</label>
                                        <div class="col-sm-8">
                                            <input type="text" name="bank_branch" class="form-control"
                                                value="{{ $teacher->teacherDetail ? $teacher->teacherDetail->bank_branch : '' }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-right">
                        <button type="submit" class="btn btn-primary">Update Teacher</button>
                    </div>
                </form>
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

    .form-control {
        background-color: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        color: #fff;
    }

    .form-control:focus {
        background-color: rgba(255, 255, 255, 0.15);
        border-color: rgba(255, 255, 255, 0.3);
        color: #fff;
    }

    .form-control::placeholder {
        color: rgba(255, 255, 255, 0.5);
    }

    .form-group label {
        color: rgba(255, 255, 255, 0.8);
    }

    select.form-control {
        background-color: rgba(255, 255, 255, 0.1);
    }

    textarea.form-control {
        background-color: rgba(255, 255, 255, 0.1);
    }
</style>
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
        // Initialize tooltips
        $('[data-toggle="tooltip"]').tooltip();

        // Handle profile picture change
        $('#profile_picture').change(function() {
            if (this.files && this.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('.profile-image-container img').attr('src', e.target.result);
                }
                reader.readAsDataURL(this.files[0]);
            }
        });
    });
</script>
@endpush
@endsection