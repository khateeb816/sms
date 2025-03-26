@extends('backend.layouts.app')

@section('title', 'Create Exam')

@section('content')
<div class="content-wrapper">
    <div class="container-fluid">
        <!-- Breadcrumb-->
        <div class="row pt-2 pb-2">
            <div class="col-sm-9">
                <h4 class="page-title">Create Exam</h4>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('exams.index') }}">Exams</a></li>
                    <li class="breadcrumb-item active">Create Exam</li>
                </ol>
            </div>
        </div>
        <!-- End Breadcrumb-->

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Create New Exam</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('exams.store') }}" method="POST">
                            @csrf

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="title">Title <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" required>
                                        @error('title')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="class_id">Class <span class="text-danger">*</span></label>
                                        <select class="form-control @error('class_id') is-invalid @enderror" id="class_id" name="class_id" required>
                                            <option value="">Select Class</option>
                                            @foreach($classes as $class)
                                                <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>
                                                    {{ $class->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('class_id')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                @if(Auth::user()->role === 1)
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="teacher_id">Teacher <span class="text-danger">*</span></label>
                                        <select class="form-control @error('teacher_id') is-invalid @enderror" id="teacher_id" name="teacher_id" required>
                                            <option value="">Select Teacher</option>
                                            @foreach(App\Models\User::where('role', 2)->get() as $teacher)
                                                <option value="{{ $teacher->id }}" {{ old('teacher_id') == $teacher->id ? 'selected' : '' }}>
                                                    {{ $teacher->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('teacher_id')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                @endif

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="subject">Subject <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('subject') is-invalid @enderror" id="subject" name="subject" value="{{ old('subject') }}" required>
                                        @error('subject')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="exam_date">Exam Date <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control @error('exam_date') is-invalid @enderror" id="exam_date" name="exam_date" value="{{ old('exam_date', date('Y-m-d')) }}" required>
                                        @error('exam_date')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="start_time">Start Time <span class="text-danger">*</span></label>
                                        <input type="time" class="form-control @error('start_time') is-invalid @enderror" id="start_time" name="start_time" value="{{ old('start_time', '09:00') }}" required>
                                        @error('start_time')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="end_time">End Time <span class="text-danger">*</span></label>
                                        <input type="time" class="form-control @error('end_time') is-invalid @enderror" id="end_time" name="end_time" value="{{ old('end_time', '11:00') }}" required>
                                        @error('end_time')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="total_marks">Total Marks <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control @error('total_marks') is-invalid @enderror" id="total_marks" name="total_marks" value="{{ old('total_marks', 100) }}" min="1" required>
                                        @error('total_marks')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="passing_marks">Passing Marks <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control @error('passing_marks') is-invalid @enderror" id="passing_marks" name="passing_marks" value="{{ old('passing_marks', 33) }}" min="1" required>
                                        @error('passing_marks')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="type">Exam Type <span class="text-danger">*</span></label>
                                        <select class="form-control @error('type') is-invalid @enderror" id="type" name="type" required>
                                            <option value="first_term" {{ old('type') == 'first_term' ? 'selected' : '' }}>First Term</option>
                                            <option value="second_term" {{ old('type') == 'second_term' ? 'selected' : '' }}>Second Term</option>
                                            <option value="third_term" {{ old('type') == 'third_term' ? 'selected' : '' }}>Third Term</option>
                                            <option value="final_term" {{ old('type') == 'final_term' ? 'selected' : '' }}>Final Term</option>
                                        </select>
                                        @error('type')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="description">Description</label>
                                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                                        @error('description')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="instructions">Instructions</label>
                                        <textarea class="form-control @error('instructions') is-invalid @enderror" id="instructions" name="instructions" rows="3">{{ old('instructions') }}</textarea>
                                        @error('instructions')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mt-4">
                                <button type="submit" class="btn btn-primary">Create Exam</button>
                                <a href="{{ route('exams.index') }}" class="btn btn-outline-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Validate passing marks against total marks
        $('#passing_marks, #total_marks').on('input', function() {
            var total = parseInt($('#total_marks').val()) || 0;
            var passing = parseInt($('#passing_marks').val()) || 0;

            if (passing > total) {
                $('#passing_marks').val(total);
            }
        });

        // Validate end time is after start time
        $('#start_time, #end_time').on('change', function() {
            var start = $('#start_time').val();
            var end = $('#end_time').val();

            if (start && end && start >= end) {
                alert('End time must be after start time');
                $('#end_time').val('');
            }
        });
    });
</script>
@endpush
