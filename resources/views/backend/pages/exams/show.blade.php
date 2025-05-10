@extends('backend.layouts.app')

@section('title', 'Exam Details')

@section('content')
<div class=" ">
    <div class="container-fluid">
        <!-- Breadcrumb-->
        <div class="row pt-2 pb-2">
            <div class="col-sm-9">
                <h4 class="page-title">Exam Details</h4>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('exams.index') }}">Exams</a></li>
                    <li class="breadcrumb-item active">Exam Details</li>
                </ol>
            </div>
            <div class="col-sm-3">
                <div class="btn-group float-sm-right">
                    @if(auth()->id() === $exam->teacher_id || auth()->user()->role === 1)
                    @if(in_array($exam->status, ['scheduled', 'in_progress']))
                    <a href="{{ route('exams.results', $exam) }}" class="btn btn-success waves-effect waves-light">
                        <i class="fa fa-plus mr-1"></i> Manage Results
                    </a>
                    @endif
                    <a href="{{ route('exams.edit', $exam) }}" class="btn btn-warning waves-effect waves-light ml-2">
                        <i class="fa fa-edit mr-1"></i> Edit
                    </a>
                    @endif
                </div>
            </div>
        </div>
        <!-- End Breadcrumb-->

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h5>{{ $exam->title }}</h5>
                        <div class="btn-group float-sm-right">
                            @if($exam->status === 'completed')
                            <a href="{{ route('exams.results.print', $exam) }}" class="btn btn-primary">
                                <i class="fa fa-print"></i> Print Results
                            </a>
                            @endif
                            <a href="{{ route('exams.edit', $exam) }}" class="btn btn-info">
                                <i class="fa fa-edit"></i> Edit
                            </a>
                            <a href="{{ route('exams.index') }}" class="btn btn-secondary">
                                <i class="fa fa-arrow-left"></i> Back
                            </a>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-4">
                                <strong>Class:</strong> {{ $exam->class->name }}
                            </div>
                            <div class="col-md-4">
                                <strong>Subject:</strong> {{ $exam->subject }}
                            </div>
                            <div class="col-md-4">
                                <p class="mb-1"><strong>Type:</strong></p>
                                <p>{{ ucwords(str_replace('_', ' ', $exam->type)) }}</p>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-4">
                                <strong>Date:</strong> {{ $exam->exam_date->format('M d, Y') }}
                            </div>
                            <div class="col-md-4">
                                <strong>Time:</strong>
                                {{ \Carbon\Carbon::parse($exam->start_time)->format('h:i A') }} -
                                {{ \Carbon\Carbon::parse($exam->end_time)->format('h:i A') }}
                            </div>
                            <div class="col-md-4">
                                <strong>Status:</strong>
                                @php
                                $statusClass = [
                                'scheduled' => 'primary',
                                'in_progress' => 'warning',
                                'completed' => 'success',
                                'cancelled' => 'danger'
                                ][$exam->status];
                                @endphp
                                <span class="badge badge-pill badge-{{ $statusClass }} text-uppercase">
                                    {{ $exam->status }}
                                </span>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-4">
                                <strong>Total Marks:</strong> {{ $exam->total_marks }}
                            </div>
                            <div class="col-md-4">
                                <strong>Passing Marks:</strong> {{ $exam->passing_marks }}
                            </div>
                            <div class="col-md-4">
                                <strong>Teacher:</strong> {{ $exam->teacher->name }}
                            </div>
                        </div>
                        @if($exam->description)
                        <div class="row mt-3">
                            <div class="col-12">
                                <strong>Description:</strong><br>
                                {{ $exam->description }}
                            </div>
                        </div>
                        @endif
                        @if($exam->instructions)
                        <div class="row mt-3">
                            <div class="col-12">
                                <strong>Instructions:</strong><br>
                                {{ $exam->instructions }}
                            </div>
                        </div>
                        @endif
                    </div>

                    @if($exam->results->isNotEmpty())
                    <div class="card-body">
                        <h5 class="mb-4">Exam Results</h5>
                        <div class="table-responsive">
                            <table id="default-datatable" class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Student Name</th>
                                        <th>Roll Number</th>
                                        <th>Marks Obtained</th>
                                        <th>Percentage</th>
                                        <th>Grade</th>
                                        <th>Status</th>
                                        <th>Remarks</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($exam->results as $result)
                                    <tr>
                                        <td>{{ $result->student->name }}</td>
                                        <td>{{ $result->student->roll_number }}</td>
                                        <td>{{ $result->marks_obtained }}/{{ $exam->total_marks }}</td>
                                        <td>{{ number_format($result->percentage, 2) }}%</td>
                                        <td>
                                            <span class="badge badge-pill badge-info">
                                                {{ $result->grade }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($result->is_passed)
                                            <span class="badge badge-pill badge-success">Passed</span>
                                            @else
                                            <span class="badge badge-pill badge-danger">Failed</span>
                                            @endif
                                        </td>
                                        <td>{{ $result->remarks }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if($exam->status === 'completed')
                        <div class="row mt-4">
                            <div class="col-md-3">
                                <div class="card bg-success text-white">
                                    <div class="card-body">
                                        <h5 class="card-title text-white">Pass Rate</h5>
                                        <h4 class="mb-0">{{ number_format(($exam->results->where('is_passed',
                                            true)->count() / $exam->results->count()) * 100, 1) }}%</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-info text-white">
                                    <div class="card-body">
                                        <h5 class="card-title text-white">Average Score</h5>
                                        <h4 class="mb-0">{{ number_format($exam->results->avg('percentage'), 1) }}%</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-warning text-white">
                                    <div class="card-body">
                                        <h5 class="card-title text-white">Highest Score</h5>
                                        <h4 class="mb-0">{{ number_format($exam->results->max('percentage'), 1) }}%</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-danger text-white">
                                    <div class="card-body">
                                        <h5 class="card-title text-white">Lowest Score</h5>
                                        <h4 class="mb-0">{{ number_format($exam->results->min('percentage'), 1) }}%</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@if($exam->results->isNotEmpty())
<script>
    $(document).ready(function() {
        $('#default-datatable').DataTable({
            "order": [[2, "desc"]],
            "columnDefs": [
                { "orderable": false, "targets": 6 }
            ]
        });
    });
</script>
@endif
@endpush
