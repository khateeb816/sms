@extends('backend.layouts.app')

@section('title', 'Add New Teacher')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Add New Teacher</h3>
                <div class="card-action">
                    <a href="{{ route('teachers.index') }}" class="btn btn-secondary">Back to List</a>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('teachers.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <!-- Basic Information Card -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h4 class="card-title">Basic Information</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group row">
                                        <label class="col-sm-4 col-form-label">Name <span
                                                class="text-danger">*</span>:</label>
                                        <div class="col-sm-8">
                                            <input type="text" name="name"
                                                class="form-control @error('name') is-invalid @enderror"
                                                value="{{ old('name') }}" required>
                                            @error('name')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-4 col-form-label">Email <span
                                                class="text-danger">*</span>:</label>
                                        <div class="col-sm-8">
                                            <input type="email" name="email"
                                                class="form-control @error('email') is-invalid @enderror"
                                                value="{{ old('email') }}" required>
                                            @error('email')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-4 col-form-label">Password <span
                                                class="text-danger">*</span>:</label>
                                        <div class="col-sm-8">
                                            <input type="password" name="password"
                                                class="form-control @error('password') is-invalid @enderror" required>
                                            @error('password')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-4 col-form-label">Confirm Password <span
                                                class="text-danger">*</span>:</label>
                                        <div class="col-sm-8">
                                            <input type="password" name="password_confirmation" class="form-control"
                                                required>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-4 col-form-label">Phone <small
                                                class="text-muted">(Optional)</small>:</label>
                                        <div class="col-sm-8">
                                            <input type="text" name="phone"
                                                class="form-control @error('phone') is-invalid @enderror"
                                                value="{{ old('phone') }}">
                                            @error('phone')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-4 col-form-label">Qualification <small
                                                class="text-muted">(Optional)</small>:</label>
                                        <div class="col-sm-8">
                                            <input type="text" name="qualification"
                                                class="form-control @error('qualification') is-invalid @enderror"
                                                value="{{ old('qualification') }}">
                                            @error('qualification')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-4 col-form-label">Specialization <small
                                                class="text-muted">(Optional)</small>:</label>
                                        <div class="col-sm-8">
                                            <input type="text" name="specialization"
                                                class="form-control @error('specialization') is-invalid @enderror"
                                                value="{{ old('specialization') }}">
                                            @error('specialization')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-4 col-form-label">Address <small
                                                class="text-muted">(Optional)</small>:</label>
                                        <div class="col-sm-8">
                                            <textarea name="address"
                                                class="form-control @error('address') is-invalid @enderror"
                                                rows="3">{{ old('address') }}</textarea>
                                            @error('address')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-4 col-form-label">Status <span
                                                class="text-danger">*</span>:</label>
                                        <div class="col-sm-8">
                                            <select name="status"
                                                class="form-control @error('status') is-invalid @enderror">
                                                <option value="active" {{ old('status')=='active' ? 'selected' : '' }}>
                                                    Active</option>
                                                <option value="inactive" {{ old('status')=='inactive' ? 'selected' : ''
                                                    }}>Inactive</option>
                                            </select>
                                            @error('status')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
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
                                        <label class="col-sm-4 col-form-label">Education Level <small
                                                class="text-muted">(Optional)</small>:</label>
                                        <div class="col-sm-8">
                                            <input type="text" name="education_level"
                                                class="form-control @error('education_level') is-invalid @enderror"
                                                value="{{ old('education_level') }}">
                                            @error('education_level')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-4 col-form-label">University <small
                                                class="text-muted">(Optional)</small>:</label>
                                        <div class="col-sm-8">
                                            <input type="text" name="university"
                                                class="form-control @error('university') is-invalid @enderror"
                                                value="{{ old('university') }}">
                                            @error('university')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-4 col-form-label">Degree <small
                                                class="text-muted">(Optional)</small>:</label>
                                        <div class="col-sm-8">
                                            <input type="text" name="degree"
                                                class="form-control @error('degree') is-invalid @enderror"
                                                value="{{ old('degree') }}">
                                            @error('degree')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-4 col-form-label">Major <small
                                                class="text-muted">(Optional)</small>:</label>
                                        <div class="col-sm-8">
                                            <input type="text" name="major"
                                                class="form-control @error('major') is-invalid @enderror"
                                                value="{{ old('major') }}">
                                            @error('major')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-4 col-form-label">Graduation Year <small
                                                class="text-muted">(Optional)</small>:</label>
                                        <div class="col-sm-8">
                                            <input type="number" name="graduation_year"
                                                class="form-control @error('graduation_year') is-invalid @enderror"
                                                value="{{ old('graduation_year') }}" min="1900"
                                                max="{{ date('Y') + 10 }}">
                                            @error('graduation_year')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-4 col-form-label">Certification <small
                                                class="text-muted">(Optional)</small>:</label>
                                        <div class="col-sm-8">
                                            <input type="text" name="certification"
                                                class="form-control @error('certification') is-invalid @enderror"
                                                value="{{ old('certification') }}">
                                            @error('certification')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label>Teaching Experience <small class="text-muted">(Optional)</small>:</label>
                                        <textarea name="teaching_experience"
                                            class="form-control @error('teaching_experience') is-invalid @enderror"
                                            rows="4">{{ old('teaching_experience') }}</textarea>
                                        @error('teaching_experience')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label>Biography <small class="text-muted">(Optional)</small>:</label>
                                        <textarea name="biography"
                                            class="form-control @error('biography') is-invalid @enderror"
                                            rows="4">{{ old('biography') }}</textarea>
                                        @error('biography')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
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
                                        <label class="col-sm-4 col-form-label">Contact Name <small
                                                class="text-muted">(Optional)</small>:</label>
                                        <div class="col-sm-8">
                                            <input type="text" name="emergency_contact_name"
                                                class="form-control @error('emergency_contact_name') is-invalid @enderror"
                                                value="{{ old('emergency_contact_name') }}">
                                            @error('emergency_contact_name')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-4 col-form-label">Contact Phone <small
                                                class="text-muted">(Optional)</small>:</label>
                                        <div class="col-sm-8">
                                            <input type="text" name="emergency_contact_phone"
                                                class="form-control @error('emergency_contact_phone') is-invalid @enderror"
                                                value="{{ old('emergency_contact_phone') }}">
                                            @error('emergency_contact_phone')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-4 col-form-label">Relationship <small
                                                class="text-muted">(Optional)</small>:</label>
                                        <div class="col-sm-8">
                                            <input type="text" name="emergency_contact_relationship"
                                                class="form-control @error('emergency_contact_relationship') is-invalid @enderror"
                                                value="{{ old('emergency_contact_relationship') }}">
                                            @error('emergency_contact_relationship')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
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
                                        <label class="col-sm-4 col-form-label">Bank Name <small
                                                class="text-muted">(Optional)</small>:</label>
                                        <div class="col-sm-8">
                                            <input type="text" name="bank_name"
                                                class="form-control @error('bank_name') is-invalid @enderror"
                                                value="{{ old('bank_name') }}">
                                            @error('bank_name')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-4 col-form-label">Account Number <small
                                                class="text-muted">(Optional)</small>:</label>
                                        <div class="col-sm-8">
                                            <input type="text" name="bank_account_number"
                                                class="form-control @error('bank_account_number') is-invalid @enderror"
                                                value="{{ old('bank_account_number') }}">
                                            @error('bank_account_number')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-4 col-form-label">Branch <small
                                                class="text-muted">(Optional)</small>:</label>
                                        <div class="col-sm-8">
                                            <input type="text" name="bank_branch"
                                                class="form-control @error('bank_branch') is-invalid @enderror"
                                                value="{{ old('bank_branch') }}">
                                            @error('bank_branch')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-right">
                        <button type="submit" class="btn btn-primary">Save Teacher</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
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

    .invalid-feedback {
        color: #ff6b6b;
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }

    .text-muted {
        color: rgba(255, 255, 255, 0.5) !important;
    }
</style>
@endpush
@endsection