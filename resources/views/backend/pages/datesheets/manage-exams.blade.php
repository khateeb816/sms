@extends('backend.layouts.app')

@section('title', 'Manage Datesheet Exams')

@section('content')
<div class="content-wrapper">
    <div class="container-fluid">
        <!-- Breadcrumb-->
        <div class="row pt-2 pb-2">
            <div class="col-sm-9">
                <h4 class="page-title">Manage Datesheet Exams</h4>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('datesheets.index') }}">Datesheets</a></li>
                    <li class="breadcrumb-item active">Manage Exams</li>
                </ol>
            </div>
        </div>
        <!-- End Breadcrumb-->

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h5>{{ $datesheet->title }} - {{ $datesheet->class->name }}</h5>
                        <div class="row mt-3">
                            <div class="col-md-4">
                                <strong>Term:</strong> {{ ucfirst($datesheet->term) }} Term
                            </div>
                            <div class="col-md-4">
                                <strong>Start Date:</strong> {{ $datesheet->start_date->format('M d, Y') }}
                            </div>
                            <div class="col-md-4">
                                <strong>End Date:</strong> {{ $datesheet->end_date->format('M d, Y') }}
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <form action="{{ route('datesheets.update-exams', $datesheet) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Day Number</th>
                                            <th>Subject</th>
                                            <th>Date</th>
                                            <th>Time</th>
                                            <th>Total Marks</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="exam-list">
                                        @foreach($datesheet->exams as $exam)
                                        <tr>
                                            <td>
                                                <input type="number" class="form-control"
                                                    name="exams[{{ $loop->index }}][day_number]"
                                                    value="{{ $exam->pivot->day_number }}" min="1" required>
                                                <input type="hidden" name="exams[{{ $loop->index }}][exam_id]"
                                                    value="{{ $exam->id }}">
                                            </td>
                                            <td>{{ $exam->subject }}</td>
                                            <td>{{ $exam->exam_date->format('M d, Y') }}</td>
                                            <td>{{ $exam->start_time->format('h:i A') }} - {{ $exam->end_time->format('h:i A')
                                                }}</td>
                                            <td>{{ $exam->total_marks }}</td>
                                            <td>
                                                <button type="button" class="btn btn-danger btn-sm remove-exam">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            @if($availableExams->isNotEmpty())
                            <div class="mt-4">
                                <h5>Available Exams</h5>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Subject</th>
                                                <th>Date</th>
                                                <th>Time</th>
                                                <th>Total Marks</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($availableExams as $exam)
                                            <tr>
                                                <td>{{ $exam->subject }}</td>
                                                <td>{{ $exam->exam_date->format('M d, Y') }}</td>
                                                <td>{{ $exam->start_time->format('h:i A') }} - {{
                                                    $exam->end_time->format('h:i A') }}</td>
                                                <td>{{ $exam->total_marks }}</td>
                                                <td>
                                                    <button type="button" class="btn btn-success btn-sm add-exam"
                                                        data-exam-id="{{ $exam->id }}" data-subject="{{ $exam->subject }}"
                                                        data-date="{{ $exam->exam_date->format('M d, Y') }}"
                                                        data-time="{{ $exam->start_time->format('h:i A') }} - {{ $exam->end_time->format('h:i A') }}"
                                                        data-marks="{{ $exam->total_marks }}">
                                                        <i class="fa fa-plus"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            @endif

                            <div class="form-group mt-4">
                                <button type="submit" class="btn btn-primary px-5">Save Changes</button>
                                <a href="{{ route('datesheets.show', $datesheet) }}"
                                    class="btn btn-outline-primary px-5">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add exam to the list
        document.addEventListener('click', function(e) {
            if (e.target.closest('.add-exam')) {
                const button = e.target.closest('.add-exam');
                const examId = button.dataset.examId;
                const subject = button.dataset.subject;
                const date = button.dataset.date;
                const time = button.dataset.time;
                const marks = button.dataset.marks;
                const examList = document.getElementById('exam-list');
                const index = examList.getElementsByTagName('tr').length;

                const newRow = document.createElement('tr');
                newRow.innerHTML = `
                <td>
                    <input type="number" class="form-control" name="exams[${index}][day_number]" value="${index + 1}" min="1" required>
                    <input type="hidden" name="exams[${index}][exam_id]" value="${examId}">
                    </td>
                    <td>${subject}</td>
                    <td>${date}</td>
                    <td>${time}</td>
                    <td>${marks}</td>
                    <td>
                        <button type="button" class="btn btn-danger btn-sm remove-exam">
                            <i class="fa fa-trash"></i>
                        </button>
                    </td>
                    `;

                    examList.appendChild(newRow);
                    button.closest('tr').remove();
                }
            });

        // Remove exam from the list
        document.addEventListener('click', function(e) {
            if (e.target.closest('.remove-exam')) {
                e.target.closest('tr').remove();
            }
        });
    });
</script>

@endsection