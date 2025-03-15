@extends('backend.layouts.app')

@section('title', 'Edit Timetable Entry')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Edit Timetable Entry</h3>
                <div class="card-action">
                    <a href="{{ route('timetable.index') }}" class="btn btn-secondary">Back to List</a>
                </div>
            </div>
            <div class="card-body">
                @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
                @endif

                <form action="{{ route('timetable.update', $timetable->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="class_id">Class <span class="text-danger">*</span></label>
                                <select class="form-control @error('class_id') is-invalid @enderror" id="class_id"
                                    name="class_id" required>
                                    <option value="">Select Class</option>
                                    @foreach($classes as $class)
                                    <option value="{{ $class->id }}" {{ (old('class_id', $timetable->class_id) ==
                                        $class->id) ? 'selected' : '' }}>
                                        {{ $class->name }} ({{ $class->grade_year }})
                                    </option>
                                    @endforeach
                                </select>
                                @error('class_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="day_of_week">Day <span class="text-danger">*</span></label>
                                <select class="form-control @error('day_of_week') is-invalid @enderror" id="day_of_week"
                                    name="day_of_week" required>
                                    <option value="">Select Day</option>
                                    @foreach($days as $day)
                                    <option value="{{ $day }}" {{ (old('day_of_week', $timetable->day_of_week) == $day)
                                        ? 'selected' : '' }}>
                                        {{ $day }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('day_of_week')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="period_id">Period <span class="text-danger">*</span></label>
                                <select class="form-control @error('period_id') is-invalid @enderror" id="period_id"
                                    name="period_id" required>
                                    <option value="">Select Period</option>
                                    @foreach($periods as $period)
                                    <option value="{{ $period->id }}" {{ (old('period_id', $timetable->period_id) ==
                                        $period->id) ? 'selected' : '' }}>
                                        {{ $period->name }} ({{ $period->start_time->format('H:i') }} - {{
                                        $period->end_time->format('H:i') }})
                                    </option>
                                    @endforeach
                                </select>
                                @error('period_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="subject">Subject <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('subject') is-invalid @enderror"
                                    id="subject" name="subject" value="{{ old('subject', $timetable->subject) }}"
                                    required>
                                @error('subject')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="teacher_id">Teacher</label>
                                <select class="form-control @error('teacher_id') is-invalid @enderror" id="teacher_id"
                                    name="teacher_id">
                                    <option value="">Select Teacher</option>
                                    @foreach($teachers as $teacher)
                                    <option value="{{ $teacher->id }}" {{ old('teacher_id', $timetable->teacher_id) ==
                                        $teacher->id ? 'selected' : '' }}>
                                        {{ $teacher->name }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('teacher_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="notes">Notes</label>
                                <textarea class="form-control @error('notes') is-invalid @enderror" id="notes"
                                    name="notes" rows="3">{{ old('notes', $timetable->notes) }}</textarea>
                                @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="is_break" name="is_break"
                                        value="1" {{ old('is_break', $timetable->is_break) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="is_break">This is a break period</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Update Timetable Entry</button>
                        <a href="{{ route('timetable.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // When is_break is checked, disable subject and teacher fields
        const isBreakCheckbox = document.getElementById('is_break');
        const subjectField = document.getElementById('subject');
        const teacherField = document.getElementById('teacher_id');
        const form = document.querySelector('form');
        
        function toggleFields() {
            if (isBreakCheckbox.checked) {
                subjectField.value = 'BREAK';
                subjectField.setAttribute('readonly', true);
                teacherField.value = '';
                teacherField.setAttribute('disabled', true);
            } else {
                subjectField.removeAttribute('readonly');
                teacherField.removeAttribute('disabled');
            }
        }
        
        isBreakCheckbox.addEventListener('change', toggleFields);
        
        // Handle form submission to ensure disabled fields are included
        form.addEventListener('submit', function(e) {
            // If break is checked, make sure subject is set to BREAK
            if (isBreakCheckbox.checked) {
                // Create a hidden input for subject if it's disabled
                const hiddenSubject = document.createElement('input');
                hiddenSubject.type = 'hidden';
                hiddenSubject.name = 'subject';
                hiddenSubject.value = 'BREAK';
                form.appendChild(hiddenSubject);
            }
        });
        
        // Initial toggle
        toggleFields();
    });
</script>
@endsection