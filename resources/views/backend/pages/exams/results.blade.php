@extends('backend.layouts.app')

@section('title', 'Manage Exam Results')

@section('content')
<div class=" ">
    <div class="container-fluid">
        <!-- Breadcrumb-->
        <div class="row pt-2 pb-2">
            <div class="col-sm-9">
                <h4 class="page-title">Manage Exam Results</h4>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('exams.index') }}">Exams</a></li>
                    <li class="breadcrumb-item active">Manage Results</li>
                </ol>
            </div>
        </div>
        <!-- End Breadcrumb-->

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h5>{{ $exam->title }}</h5>
                        <div class="row mt-3">
                            <div class="col-md-4">
                                <strong>Class:</strong> {{ $exam->class->name }}
                            </div>
                            <div class="col-md-4">
                                <strong>Subject:</strong> {{ $exam->subject }}
                            </div>
                            <div class="col-md-4">
                                <strong>Type:</strong>
                                <span class="badge badge-pill badge-info text-uppercase">
                                    {{ ucwords(str_replace('_', ' ', $exam->type)) }}
                                </span>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-4">
                                <strong>Total Marks:</strong> {{ $exam->total_marks }}
                            </div>
                            <div class="col-md-4">
                                <strong>Date:</strong> {{ $exam->exam_date->format('M d, Y') }}
                            </div>
                            <div class="col-md-4">
                                <strong>Time:</strong>
                                {{ \Carbon\Carbon::parse($exam->start_time)->format('h:i A') }} -
                                {{ \Carbon\Carbon::parse($exam->end_time)->format('h:i A') }}
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-4">
                                <strong>Passing Marks:</strong> {{ $exam->passing_marks }}
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('exams.results.store', $exam) }}" method="POST">
                            @csrf
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Student Name</th>
                                            <th>Roll Number</th>
                                            <th>Marks Obtained</th>
                                            <th>Remarks</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($exam->class->students as $student)
                                        @php
                                        $result = $exam->results->where('student_id', $student->id)->first();
                                        @endphp
                                        <tr>
                                            <td>
                                                {{ $student->name }}
                                                <input type="hidden" name="results[{{ $loop->index }}][student_id]"
                                                    value="{{ $student->id }}">
                                            </td>
                                            <td>{{ $student->roll_number }}</td>
                                            <td>
                                                <input type="number"
                                                    class="form-control @error('results.'.$loop->index.'.marks_obtained') is-invalid @enderror"
                                                    name="results[{{ $loop->index }}][marks_obtained]"
                                                    value="{{ old('results.'.$loop->index.'.marks_obtained', $result->marks_obtained ?? '') }}"
                                                    min="0" max="{{ $exam->total_marks }}" required>
                                                @error('results.'.$loop->index.'.marks_obtained')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </td>
                                            <td>
                                                <input type="text"
                                                    class="form-control @error('results.'.$loop->index.'.remarks') is-invalid @enderror"
                                                    name="results[{{ $loop->index }}][remarks]"
                                                    value="{{ old('results.'.$loop->index.'.remarks', $result->remarks ?? '') }}"
                                                    placeholder="Optional remarks">
                                                @error('results.'.$loop->index.'.remarks')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="form-group mt-4">
                                <button type="submit" class="btn btn-primary px-5">Save Results</button>
                                <a href="{{ route('exams.show', $exam) }}"
                                    class="btn btn-outline-primary px-5">Cancel</a>
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
    // Validate marks against total marks
    $('input[type="number"]').on('input', function() {
        var value = parseInt($(this).val()) || 0;
        var max = parseInt($(this).attr('max')) || 0;

        if (value > max) {
            $(this).val(max);
        }
        if (value < 0) {
            $(this).val(0);
        }
    });
});
</script>
@endpush
