@extends('backend.layouts.app')

@section('title', 'Submit Complaint')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Submit Complaint</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('complaints.index') }}">Complaints</a></li>
                        <li class="breadcrumb-item active">Submit</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Submit New Complaint</h3>
                        </div>
                        <form action="{{ route('complaints.store') }}" method="POST">
                            @csrf
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="complaint_type">Complaint Type</label>
                                    <select name="complaint_type" id="complaint_type"
                                        class="form-control @error('complaint_type') is-invalid @enderror" required>
                                        <option value="">Select Type</option>
                                        @if(auth()->user()->role === 2)
                                        <option value="against_teacher" {{ old('complaint_type')==='against_teacher'
                                            ? 'selected' : '' }}>Against Teacher</option>
                                        <option value="against_student" {{ old('complaint_type')==='against_student'
                                            ? 'selected' : '' }}>Against Student</option>
                                        <option value="against_parent" {{ old('complaint_type')==='against_parent'
                                            ? 'selected' : '' }}>Against Parent</option>
                                        @elseif(auth()->user()->role === 3)
                                        <option value="against_teacher" {{ old('complaint_type')==='against_teacher'
                                            ? 'selected' : '' }}>Against Teacher</option>
                                        @endif
                                        <option value="against_admin" {{ old('complaint_type')==='against_admin'
                                            ? 'selected' : '' }}>Against Admin</option>
                                        <option value="general" {{ old('complaint_type')==='general' ? 'selected' : ''
                                            }}>General</option>
                                    </select>
                                    @error('complaint_type')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group" id="user_select_group" style="display: none;">
                                    <label for="against_user_id">Select User</label>
                                    <select name="against_user_id" id="against_user_id"
                                        class="form-control @error('against_user_id') is-invalid @enderror">
                                        <option value="">Select User</option>
                                    </select>
                                    @error('against_user_id')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="subject">Subject</label>
                                    <input type="text" name="subject" id="subject"
                                        class="form-control @error('subject') is-invalid @enderror"
                                        value="{{ old('subject') }}" required>
                                    @error('subject')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="description">Description</label>
                                    <textarea name="description" id="description" rows="5"
                                        class="form-control @error('description') is-invalid @enderror"
                                        required>{{ old('description') }}</textarea>
                                    @error('description')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Submit Complaint</button>
                                <a href="{{ route('complaints.index') }}" class="btn btn-default float-right">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var complaintTypeSelect = document.getElementById('complaint_type');
        var userSelectGroup = document.getElementById('user_select_group');
        var againstUserSelect = document.getElementById('against_user_id');
        
        complaintTypeSelect.addEventListener('change', function() {
            // Clear previous options and error messages
            var errorMessages = userSelectGroup.querySelectorAll('.text-danger');
            errorMessages.forEach(function(element) {
                element.remove();
            });
            
            // Clear select options
            againstUserSelect.innerHTML = '<option value="">Select User</option>';
            
            if (['against_teacher', 'against_student', 'against_parent'].includes(this.value)) {
                console.log('Selected type:', this.value); // Debug log
                
                // Show the user select group
                userSelectGroup.style.display = 'block';
                againstUserSelect.required = true;
                
                // Fetch users based on complaint type
                var xhr = new XMLHttpRequest();
                xhr.open('GET', '/dash/users/by-role/' + this.value, true);
                xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                xhr.setRequestHeader('Accept', 'application/json');
                
                xhr.onload = function() {
                    if (xhr.status >= 200 && xhr.status < 300) {
                        var users = JSON.parse(xhr.responseText);
                        console.log('Fetched users:', users);
                        
                        if (users.length === 0) {
                            var errorDiv = document.createElement('div');
                            errorDiv.className = 'text-danger';
                            errorDiv.textContent = 'No users found for this category.';
                            userSelectGroup.appendChild(errorDiv);
                        } else {
                            users.forEach(function(user) {
                                var option = document.createElement('option');
                                option.value = user.id;
                                option.textContent = user.name;
                                againstUserSelect.appendChild(option);
                            });
                        }
                    } else {
                        console.error('Error fetching users:', xhr.statusText);
                        var errorDiv = document.createElement('div');
                        errorDiv.className = 'text-danger';
                        errorDiv.textContent = 'Error loading users. Please try again.';
                        userSelectGroup.appendChild(errorDiv);
                    }
                };
                
                xhr.onerror = function() {
                    console.error('Network error when fetching users');
                    var errorDiv = document.createElement('div');
                    errorDiv.className = 'text-danger';
                    errorDiv.textContent = 'Network error. Please try again.';
                    userSelectGroup.appendChild(errorDiv);
                };
                
                xhr.send();
            } else {
                userSelectGroup.style.display = 'none';
                againstUserSelect.required = false;
            }
        });

        // Trigger change event on page load if complaint_type is pre-selected
        var complaintType = complaintTypeSelect.value;
        if (['against_teacher', 'against_student', 'against_parent'].includes(complaintType)) {
            // Create and dispatch a change event
            var event = new Event('change');
            complaintTypeSelect.dispatchEvent(event);
        }
    });
</script>
@endsection