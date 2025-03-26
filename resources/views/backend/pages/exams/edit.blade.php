@extends('backend.layouts.app')

@section('title', 'Edit Exam')

@section('content')
<div class="content-wrapper">
    <div class="container-fluid">
        <!-- Breadcrumb-->
        <div class="row pt-2 pb-2">
            <div class="col-sm-9">
                <h4 class="page-title">Edit Exam</h4>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('exams.index') }}">Exams</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </div>
        </div>
        <!-- End Breadcrumb-->

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('exams.update', $exam) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="title">Title <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $exam->title) }}" required>
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
                                                <option value="{{ $class->id }}" {{ old('class_id', $exam->class_id) == $class->id ? 'selected' : '' }}>
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
                                                <option value="{{ $teacher->id }}" {{ old('teacher_id', $exam->teacher_id) == $teacher->id ? 'selected' : '' }}>
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
                                        <input type="text" class="form-control @error('subject') is-invalid @enderror" id="subject" name="subject" value="{{ old('subject', $exam->subject) }}" required>
                                        @error('subject')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="exam_date" class="col-sm-2 col-form-label">Exam Date</label>
                                <div class="col-sm-10">
                                    <input type="date" class="form-control @error('exam_date') is-invalid @enderror"
                                        id="exam_date" name="exam_date"
                                        value="{{ old('exam_date', $exam->exam_date->format('Y-m-d')) }}" required>
                                    @error('exam_date')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="start_time" class="col-sm-2 col-form-label">Start Time</label>
                                <div class="col-sm-10">
                                    <input type="time" class="form-control @error('start_time') is-invalid @enderror"
                                        id="start_time" name="start_time"
                                        value="{{ old('start_time', date('H:i', strtotime($exam->start_time))) }}" required>
                                    @error('start_time')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="end_time" class="col-sm-2 col-form-label">End Time</label>
                                <div class="col-sm-10">
                                    <input type="time" class="form-control @error('end_time') is-invalid @enderror"
                                        id="end_time" name="end_time"
                                        value="{{ old('end_time', date('H:i', strtotime($exam->end_time))) }}"
                                        required>
                                    @error('end_time')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="total_marks" class="col-sm-2 col-form-label">Total Marks</label>
                                <div class="col-sm-10">
                                    <input type="number" class="form-control @error('total_marks') is-invalid @enderror"
                                        id="total_marks" name="total_marks"
                                        value="{{ old('total_marks', $exam->total_marks) }}" required min="1">
                                    @error('total_marks')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="passing_marks" class="col-sm-2 col-form-label">Passing Marks</label>
                                <div class="col-sm-10">
                                    <input type="number"
                                        class="form-control @error('passing_marks') is-invalid @enderror"
                                        id="passing_marks" name="passing_marks"
                                        value="{{ old('passing_marks', $exam->passing_marks) }}" required min="1">
                                    @error('passing_marks')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="type" class="col-sm-2 col-form-label">Exam Type</label>
                                <div class="col-sm-10">
                                    <select class="form-control @error('type') is-invalid @enderror" id="type"
                                        name="type" required>
                                        <option value="">Select Type</option>
                                        <option value="first_term" {{ old('type', $exam->type) == 'first_term' ? 'selected' : '' }}>First Term</option>
                                        <option value="second_term" {{ old('type', $exam->type) == 'second_term' ? 'selected' : '' }}>Second Term</option>
                                        <option value="third_term" {{ old('type', $exam->type) == 'third_term' ? 'selected' : '' }}>Third Term</option>
                                        <option value="final_term" {{ old('type', $exam->type) == 'final_term' ? 'selected' : '' }}>Final Term</option>
                                    </select>
                                    @error('type')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="status" class="col-sm-2 col-form-label">Status</label>
                                <div class="col-sm-10">
                                    <select class="form-control @error('status') is-invalid @enderror" id="status"
                                        name="status" required>
                                        @foreach(App\Models\Exam::getStatuses() as $value => $label)
                                        <option value="{{ $value }}" {{ old('status', $exam->status) == $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('status')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="description" class="col-sm-2 col-form-label">Description</label>
                                <div class="col-sm-10">
                                    <textarea class="form-control @error('description') is-invalid @enderror"
                                        id="description" name="description"
                                        rows="3">{{ old('description', $exam->description) }}</textarea>
                                    @error('description')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="instructions" class="col-sm-2 col-form-label">Instructions</label>
                                <div class="col-sm-10">
                                    <textarea class="form-control @error('instructions') is-invalid @enderror"
                                        id="instructions" name="instructions"
                                        rows="3">{{ old('instructions', $exam->instructions) }}</textarea>
                                    @error('instructions')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-sm-10 offset-sm-2">
                                    <button type="submit" class="btn btn-primary px-5">Update Exam</button>
                                    <a href="{{ route('exams.index') }}" class="btn btn-light px-5">Cancel</a>
                                </div>
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
    // Validate that passing marks is less than or equal to total marks
    $('#passing_marks, #total_marks').on('input', function() {
        var totalMarks = parseInt($('#total_marks').val()) || 0;
        var passingMarks = parseInt($('#passing_marks').val()) || 0;

        if (passingMarks > totalMarks) {
            $('#passing_marks').val(totalMarks);
        }
    });

    // Validate end time is after start time
    $('#start_time, #end_time').on('change', function() {
        var startTime = $('#start_time').val();
        var endTime = $('#end_time').val();

        if (startTime && endTime && startTime >= endTime) {
            alert('End time must be after start time');
            $('#end_time').val('');
        }
    });
});
</script>
@endpush
