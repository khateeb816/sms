@extends('backend.layouts.app')

@section('title', 'Create Period')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Create New Period</h3>
                <div class="card-action">
                    <a href="{{ route('periods.index') }}" class="btn btn-secondary">Back to List</a>
                </div>
            </div>
            <div class="card-body">
                @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
                @endif

                @if($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <form action="{{ route('periods.store') }}" method="POST" id="periodForm">
                    @csrf
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label">Period Name <span class="text-danger">*</span></label>
                        <div class="col-lg-9">
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name') }}" required>
                            @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label">Start Time <span class="text-danger">*</span></label>
                        <div class="col-lg-9">
                            <input type="time" name="start_time"
                                class="form-control @error('start_time') is-invalid @enderror"
                                value="{{ old('start_time') }}" required>
                            @error('start_time')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label">End Time <span class="text-danger">*</span></label>
                        <div class="col-lg-9">
                            <input type="time" name="end_time"
                                class="form-control @error('end_time') is-invalid @enderror"
                                value="{{ old('end_time') }}" required>
                            @error('end_time')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text  ">End time must be after start time</small>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label">Period Type <span class="text-danger">*</span></label>
                        <div class="col-lg-9">
                            <select name="type" class="form-control @error('type') is-invalid @enderror" required>
                                <option value="regular" {{ old('type')=='regular' ? 'selected' : '' }}>Regular</option>
                                <option value="break" {{ old('type')=='break' ? 'selected' : '' }}>Break</option>
                            </select>
                            @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label">Status</label>
                        <div class="col-lg-9">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="is_active" name="is_active"
                                    value="1" {{ old('is_active', '1' ) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="is_active">Active</label>
                            </div>
                            @error('is_active')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-9 offset-lg-3">
                            <button type="submit" class="btn btn-primary" id="submitBtn">Create Period</button>
                            <a href="{{ route('periods.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('periodForm');
        
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            console.log('Form submitted');
            
            // Get form data
            const formData = new FormData(form);
            const formDataObj = {};
            formData.forEach((value, key) => {
                formDataObj[key] = value;
            });
            
            console.log('Form data:', formDataObj);
            
            // Show loading state
            const submitBtn = document.getElementById('submitBtn');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="zmdi zmdi-spinner zmdi-hc-spin"></i> Creating...';
            
            // Submit the form
            form.submit();
        });
    });
</script>
@endpush