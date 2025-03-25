@extends('backend.layouts.app')

@section('title', 'Exam Reports')

@section('content')
<div class="content-wrapper">
    <div class="container-fluid">
        <!-- Breadcrumb-->
        <div class="row pt-2 pb-2">
            <div class="col-sm-9">
                <h4 class="page-title">Exam Reports</h4>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('exams.index') }}">Exams</a></li>
                    <li class="breadcrumb-item active">Reports</li>
                </ol>
            </div>
            <div class="col-sm-3">
                <div class="btn-group float-sm-right">
                    <a href="{{ route('exams.reports.print') }}" class="btn btn-primary">
                        <i class="fa fa-print"></i> Print All Reports
                    </a>
                </div>
            </div>
        </div>
        <!-- End Breadcrumb-->

        <!-- Summary Cards -->
        <div class="row mt-3">
            <div class="col-12 col-lg-6 col-xl-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="media">
                            <div class="media-body">
                                <h5 class="mb-0">Total Exams</h5>
                                <p class="mb-0">{{ $statistics['total_exams'] }}</p>
                            </div>
                            <div class="align-self-center"><i class="fas fa-file-alt fa-3x"></i></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-6 col-xl-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <div class="media">
                            <div class="media-body">
                                <h5 class="mb-0">Average Pass Rate</h5>
                                <p class="mb-0">{{ number_format($statistics['average_pass_rate'], 1) }}%</p>
                            </div>
                            <div class="align-self-center"><i class="fas fa-chart-line fa-3x"></i></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-6 col-xl-3">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <div class="media">
                            <div class="media-body">
                                <h5 class="mb-0">Average Score</h5>
                                <p class="mb-0">{{ number_format($statistics['average_score'], 1) }}%</p>
                            </div>
                            <div class="align-self-center"><i class="fas fa-chart-bar fa-3x"></i></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-6 col-xl-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <div class="media">
                            <div class="media-body">
                                <h5 class="mb-0">Total Students</h5>
                                <p class="mb-0">{{ $statistics['total_students'] }}</p>
                            </div>
                            <div class="align-self-center"><i class="fas fa-users fa-3x"></i></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if($exams->isEmpty())
        <div class="row mt-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center">
                        <i class="fas fa-clipboard-list fa-4x text-muted mb-3"></i>
                        <h4 class="text-muted">No Completed Exams Found</h4>
                        <p>There are no completed exams to generate reports from.</p>
                        @if(auth()->user()->role === 2)
                        <a href="{{ route('exams.create') }}" class="btn btn-primary">Create New Exam</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @else
        <!-- Exam List -->
        <div class="row mt-3">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Completed Exams</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="default-datatable" class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Class</th>
                                        <th>Subject</th>
                                        <th>Date</th>
                                        <th>Total Students</th>
                                        <th>Pass Rate</th>
                                        <th>Average Score</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($exams as $exam)
                                    <tr>
                                        <td>{{ $exam->title }}</td>
                                        <td>{{ $exam->class->name }}</td>
                                        <td>{{ $exam->subject }}</td>
                                        <td>{{ $exam->exam_date->format('M d, Y') }}</td>
                                        <td>{{ $exam->results->count() }}</td>
                                        <td>
                                            @if($exam->results->isNotEmpty())
                                            <div class="progress">
                                                @php
                                                $passRate = ($exam->results->where('is_passed', true)->count() /
                                                $exam->results->count()) * 100;
                                                @endphp
                                                <div class="progress-bar bg-success" role="progressbar"
                                                    style="width: {{ $passRate }}%" aria-valuenow="{{ $passRate }}"
                                                    aria-valuemin="0" aria-valuemax="100">
                                                    {{ number_format($passRate, 1) }}%
                                                </div>
                                            </div>
                                            @else
                                            <span class="badge badge-warning">No Results</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($exam->results->isNotEmpty())
                                            {{ number_format($exam->results->avg('percentage'), 1) }}%
                                            @else
                                            -
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('exams.show', $exam) }}"
                                                class="btn btn-info btn-sm waves-effect waves-light">
                                                <i class="fa fa-eye"></i> View Details
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Performance Analysis -->
        <div class="row mt-3">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Grade Distribution</h5>
                    </div>
                    <div class="card-body">
                        @php
                        $grades = ['A+', 'A', 'B+', 'B', 'C+', 'C', 'D', 'F'];
                        $gradeColors = [
                        'A+' => 'success',
                        'A' => 'success',
                        'B+' => 'info',
                        'B' => 'info',
                        'C+' => 'warning',
                        'C' => 'warning',
                        'D' => 'danger',
                        'F' => 'danger'
                        ];
                        $totalResults = $exams->flatMap->results->count();
                        @endphp
                        @foreach($grades as $grade)
                        @php
                        $count = $exams->flatMap->results->where('grade', $grade)->count();
                        $percentage = $totalResults > 0 ? ($count / $totalResults) * 100 : 0;
                        @endphp
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span>Grade {{ $grade }}</span>
                                <span>{{ $count }} students ({{ number_format($percentage, 1) }}%)</span>
                            </div>
                            <div class="progress" style="height: 20px;">
                                <div class="progress-bar bg-{{ $gradeColors[$grade] }}" role="progressbar"
                                    style="width: {{ $percentage }}%" aria-valuenow="{{ $percentage }}"
                                    aria-valuemin="0" aria-valuemax="100">
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Subject Performance Analysis</h5>
                    </div>
                    <div class="card-body">
                        @php
                        $subjects = $exams->groupBy('subject');
                        @endphp
                        @foreach($subjects as $subject => $subjectExams)
                        @php
                        $avgScore = $subjectExams->flatMap->results->avg('percentage') ?? 0;
                        $totalResults = $subjectExams->flatMap->results->count();
                        $passedResults = $subjectExams->flatMap->results->where('is_passed', true)->count();
                        $passRate = $totalResults > 0 ? ($passedResults / $totalResults) * 100 : 0;
                        @endphp
                        <div class="mb-4">
                            <h6>{{ $subject }}</h6>
                            <div class="row">
                                <div class="col-6">
                                    <small>Average Score</small>
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar bg-info" role="progressbar"
                                            style="width: {{ $avgScore }}%" aria-valuenow="{{ $avgScore }}"
                                            aria-valuemin="0" aria-valuemax="100">
                                            {{ number_format($avgScore, 1) }}%
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <small>Pass Rate</small>
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar bg-success" role="progressbar"
                                            style="width: {{ $passRate }}%" aria-valuenow="{{ $passRate }}"
                                            aria-valuemin="0" aria-valuemax="100">
                                            {{ number_format($passRate, 1) }}%
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#default-datatable').DataTable({
            "order": [[3, "desc"]],
            "columnDefs": [
                { "orderable": false, "targets": 7 }
            ]
        });
    });
</script>
@endpush