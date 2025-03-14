@extends('backend.layouts.app')

@section('title', 'Profile Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <div class="text-center">
                        @if(auth()->user()->image)
                        <div class="position-relative d-inline-block">
                            <img src="{{ Storage::url('profile-images/' . auth()->user()->image) }}"
                                class="rounded-circle img-thumbnail" alt="Profile Image"
                                style="width: 150px; height: 150px; object-fit: cover;">
                            <form action="{{ route('profile.photo') }}" method="POST" enctype="multipart/form-data"
                                id="photoForm" class="position-absolute" style="bottom: 0; right: 0;">
                                @csrf
                                @method('PUT')
                                <input type="file" name="photo" id="photoInput" accept="image/*" style="display: none;" 
                                    onchange="document.getElementById('photoForm').submit()">
                                <button type="button" class="btn btn-light btn-sm rounded-circle shadow-sm"
                                    onclick="document.getElementById('photoInput').click()">
                                    <i class="zmdi zmdi-edit"></i>
                                </button>
                            </form>
                        </div>
                        @else
                        <div class="position-relative d-inline-block">
                            <img src="https://ui-avatars.com/api/?name={{ auth()->user()->name }}&background=random"
                                class="rounded-circle img-thumbnail" alt="Profile Image"
                                style="width: 150px; height: 150px;">
                            <form action="{{ route('profile.photo') }}" method="POST" enctype="multipart/form-data"
                                id="photoForm" class="position-absolute" style="bottom: 0; right: 0;">
                                @csrf
                                @method('PUT')
                                <input type="file" name="photo" id="photoInput" accept="image/*" style="display: none;"
                                    onchange="document.getElementById('photoForm').submit()">
                                <button type="button" class="btn btn-light btn-sm rounded-circle shadow-sm"
                                    onclick="document.getElementById('photoInput').click()">
                                    <i class="zmdi zmdi-edit"></i>
                                </button>
                            </form>
                        </div>
                        @endif
                        <h4 class="mt-3">{{ auth()->user()->name }}</h4>
                        <p class="text-muted">
                            @if(auth()->user()->role == 1)
                            Administrator
                            @elseif(auth()->user()->role == 2)
                            Teacher
                            @elseif(auth()->user()->role == 3)
                            Parent
                            @elseif(auth()->user()->role == 4)
                            Student
                            @endif
                        </p>
                    </div>
                    <hr>
                    <div class="profile-details mt-4">
                        <h5 class="mb-3">Contact Information</h5>
                        <div class="row mb-2">
                            <div class="col-sm-4 text-muted">Email:</div>
                            <div class="col-sm-8">{{ auth()->user()->email }}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-sm-4 text-muted">Phone:</div>
                            <div class="col-sm-8">{{ auth()->user()->phone ?? 'Not set' }}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-sm-4 text-muted">Address:</div>
                            <div class="col-sm-8">{{ auth()->user()->address ?? 'Not set' }}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-sm-4 text-muted">Joined:</div>
                            <div class="col-sm-8">{{ auth()->user()->created_at->format('M d, Y') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Edit Profile</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('profile.update') }}" method="POST" id="profileForm">
                        @csrf
                        @method('PUT')

                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label">Full Name</label>
                            <div class="col-lg-9">
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                    value="{{ old('name', auth()->user()->name) }}">
                                @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label">Email</label>
                            <div class="col-lg-9">
                                <input type="email" name="email"
                                    class="form-control @error('email') is-invalid @enderror"
                                    value="{{ old('email', auth()->user()->email) }}">
                                @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label">Phone</label>
                            <div class="col-lg-9">
                                <input type="text" name="phone"
                                    class="form-control @error('phone') is-invalid @enderror"
                                    value="{{ old('phone', auth()->user()->phone) }}">
                                @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label">Address</label>
                            <div class="col-lg-9">
                                <textarea name="address" class="form-control @error('address') is-invalid @enderror"
                                    rows="3">{{ old('address', auth()->user()->address) }}</textarea>
                                @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label">Current Password</label>
                            <div class="col-lg-9">
                                <input type="password" name="current_password"
                                    class="form-control @error('current_password') is-invalid @enderror"
                                    placeholder="Enter current password">
                                @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label">New Password</label>
                            <div class="col-lg-9">
                                <input type="password" name="password"
                                    class="form-control @error('password') is-invalid @enderror"
                                    placeholder="Enter new password">
                                @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label">Confirm Password</label>
                            <div class="col-lg-9">
                                <input type="password" name="password_confirmation"
                                    class="form-control @error('password_confirmation') is-invalid @enderror"
                                    placeholder="Confirm new password">
                                @error('password_confirmation')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="text-right">
                            <button type="submit" class="btn btn-primary">Update Profile</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header">
                    <h3 class="card-title">Account Activity</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="activityTable">
                            <thead>
                                <tr>
                                    <th>Activity</th>
                                    <th>Date</th>
                                    <th>IP Address</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($activities ?? [] as $activity)
                                <tr>
                                    <td>{{ $activity->description }}</td>
                                    <td>{{ $activity->created_at->format('Y-m-d h:i A') }}</td>
                                    <td>{{ $activity->ip_address }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center">No activity records found</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Form validation and submission
    document.getElementById('profileForm').addEventListener('submit', function(e) {
        const password = document.querySelector('input[name="password"]').value;
        const confirmPassword = document.querySelector('input[name="password_confirmation"]').value;
        
        if (password && password !== confirmPassword) {
            e.preventDefault();
            alert('New password and confirm password do not match!');
            return;
        }

        // Show loading state
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="zmdi zmdi-spinner zmdi-hc-spin"></i> Updating...';

        // If there's no password, remove the current_password requirement
        if (!password) {
            const currentPasswordInput = this.querySelector('input[name="current_password"]');
            currentPasswordInput.removeAttribute('required');
        }
    });

    // Initialize DataTable for activity log
    $(function () {
        $("#activityTable").DataTable({
            "responsive": true,
            "lengthChange": false,
            "autoWidth": false,
            "order": [[1, 'desc']], // Sort by date column descending
            "pageLength": 10
        });
    });

    // Show success message if exists
    @if(session('success'))
        toastr.success('{{ session('success') }}');
    @endif

    // Show error messages if exist
    @if(session('error'))
        toastr.error('{{ session('error') }}');
    @endif
    @if($errors->any())
        @foreach($errors->all() as $error)
            toastr.error('{{ $error }}');
        @endforeach
    @endif
</script>
@endpush