@extends('backend.layouts.app')

@section('title', 'Test Details')

@section('content')
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
$totalResults = $test->results->count();
@endphp

<div class="content-wrapper">
    <div class="container-fluid">
        <!-- Breadcrumb-->
        <div class="row pt-2 pb-2">
            <div class="col-sm-9">
                <h4 class="page-title">Test Details</h4>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('tests.index') }}">Tests</a></li>
                    <li class="breadcrumb-item active">Test Details</li>
                </ol>
            </div>
            <div class="col-sm-3">
                <div class="btn-group float-sm-right">
                    @if(auth()->user()->role !== 3)
                        @if(auth()->id() === $test->teacher_id || auth()->user()->role === 1)
                            @if(in_array($test->status, ['scheduled', 'in_progress']))
                            <a href="{{ route('tests.results', $test) }}" class="btn btn-success waves-effect waves-light">
                                <i class="fa fa-plus mr-1"></i> Manage Results
                            </a>
                            @endif
                            <a href="{{ route('tests.edit', $test) }}" class="btn btn-warning waves-effect waves-light ml-2">
                                <i class="fa fa-edit mr-1"></i> Edit
                            </a>
                        @endif
                    @endif
                    <button type="button" class="btn btn-primary waves-effect waves-light" onclick="printTestReport()">
                        <i class="fas fa-print"></i> Print Report
                    </button>
                </div>
            </div>
        </div>
        <!-- End Breadcrumb-->

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h5>{{ $test->title }}</h5>
                        <div class="row mt-3">
                            <div class="col-md-4">
                                <strong>Class:</strong> {{ $test->class->name }}
                            </div>
                            <div class="col-md-4">
                                <strong>Subject:</strong> {{ $test->subject }}
                            </div>
                            <div class="col-md-4">
                                <strong>Type:</strong>
                                <span class="badge badge-pill badge-info text-uppercase">{{ $test->type }}</span>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-4">
                                <strong>Date:</strong> {{ $test->exam_date->format('M d, Y') }}
                            </div>
                            <div class="col-md-4">
                                <strong>Time:</strong>
                                {{ \Carbon\Carbon::parse($test->start_time)->format('h:i A') }} -
                                {{ \Carbon\Carbon::parse($test->end_time)->format('h:i A') }}
                            </div>
                            <div class="col-md-4">
                                <strong>Status:</strong>
                                @php
                                $statusClass = [
                                'scheduled' => 'primary',
                                'in_progress' => 'warning',
                                'completed' => 'success',
                                'cancelled' => 'danger'
                                ][$test->status];
                                @endphp
                                <span class="badge badge-pill badge-{{ $statusClass }} text-uppercase">
                                    {{ $test->status }}
                                </span>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-4">
                                <strong>Total Marks:</strong> {{ $test->total_marks }}
                            </div>
                            <div class="col-md-4">
                                <strong>Passing Marks:</strong> {{ $test->passing_marks }}
                            </div>
                            @if(auth()->user()->role !== 3)
                            <div class="col-md-4">
                                <strong>Teacher:</strong> {{ $test->teacher->name }}
                            </div>
                            @endif
                        </div>
                        @if($test->description)
                        <div class="row mt-3">
                            <div class="col-12">
                                <strong>Description:</strong><br>
                                {{ $test->description }}
                            </div>
                        </div>
                        @endif
                        @if($test->instructions)
                        <div class="row mt-3">
                            <div class="col-12">
                                <strong>Instructions:</strong><br>
                                {{ $test->instructions }}
                            </div>
                        </div>
                        @endif
                    </div>

                    @if($test->results->isNotEmpty())
                    <div class="card-body">
                        <h5 class="mb-4">Test Results</h5>
                        <div class="table-responsive">
                            <table id="test-results-table" class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Student Name</th>
                                        <th>Roll Number</th>
                                        <th>Marks Obtained</th>
                                        <th>Percentage</th>
                                        <th>Grade</th>
                                        <th>Status</th>
                                        @if(auth()->user()->role !== 3)
                                        <th>Actions</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($test->results as $result)
                                    <tr>
                                        <td>{{ $result->student->name }}</td>
                                        <td>{{ $result->student->roll_number }}</td>
                                        <td>{{ $result->marks_obtained }}/{{ $test->total_marks }}</td>
                                        <td>{{ number_format($result->percentage, 2) }}%</td>
                                        <td>
                                            <span class="badge badge-pill badge-{{ $result->is_passed ? 'success' : 'danger' }}">
                                                {{ $result->grade }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge badge-pill badge-{{ $result->is_passed ? 'success' : 'danger' }}">
                                                {{ $result->is_passed ? 'Passed' : 'Failed' }}
                                            </span>
                                        </td>
                                        @if(auth()->user()->role !== 3)
                                        <td>
                                            <a href="{{ route('tests.show', $result->id) }}" class="btn btn-info btn-sm waves-effect waves-light">
                                                <i class="fa fa-eye"></i> View Details
                                            </a>
                                        </td>
                                        @endif
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if($test->status === 'completed' && auth()->user()->role !== 3)
                        <div class="row mt-4">
                            <div class="col-md-3">
                                <div class="card bg-success text-white">
                                    <div class="card-body">
                                        <h5 class="card-title text-white">Pass Rate</h5>
                                        <h4 class="mb-0">
                                            @php
                                                $passRate = $totalResults > 0 ? ($test->results->where('is_passed', true)->count() / $totalResults) * 100 : 0;
                                            @endphp
                                            {{ number_format($passRate, 1) }}%
                                        </h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-info text-white">
                                    <div class="card-body">
                                        <h5 class="card-title text-white">Average Score</h5>
                                        <h4 class="mb-0">{{ number_format($test->results->avg('percentage'), 1) }}%</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-warning text-white">
                                    <div class="card-body">
                                        <h5 class="card-title text-white">Highest Score</h5>
                                        <h4 class="mb-0">{{ number_format($test->results->max('percentage'), 1) }}%</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-danger text-white">
                                    <div class="card-body">
                                        <h5 class="card-title text-white">Lowest Score</h5>
                                        <h4 class="mb-0">{{ number_format($test->results->min('percentage'), 1) }}%</h4>
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

        @if(auth()->user()->role !== 3)
        <!-- Performance Analysis -->
        <div class="row">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Grade Distribution</h5>
                    </div>
                    <div class="card-body">
                        @foreach($grades as $grade)
                        @php
                        $count = $test->results->where('grade', $grade)->count();
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
                        <h5>Performance Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card bg-primary text-white">
                                    <div class="card-body">
                                        <h5>Total Students</h5>
                                        <p class="mb-0">{{ $test->results->count() }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card bg-success text-white">
                                    <div class="card-body">
                                        <h5>Pass Rate</h5>
                                        <p class="mb-0">
                                            @php
                                                $passRate = $totalResults > 0 ? ($test->results->where('is_passed', true)->count() / $totalResults) * 100 : 0;
                                            @endphp
                                            {{ number_format($passRate, 1) }}%
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card bg-info text-white">
                                    <div class="card-body">
                                        <h5>Average Score</h5>
                                        <p class="mb-0">
                                            @php
                                                $avgScore = $totalResults > 0 ? $test->results->avg('percentage') : 0;
                                            @endphp
                                            {{ number_format($avgScore, 1) }}%
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card bg-warning text-white">
                                    <div class="card-body">
                                        <h5>Highest Score</h5>
                                        <p class="mb-0">
                                            @php
                                                $highestScore = $totalResults > 0 ? $test->results->max('percentage') : 0;
                                            @endphp
                                            {{ number_format($highestScore, 1) }}%
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>


<script>
    function printTestReport() {
        // Create a new window for printing
        let printWindow = window.open('', '_blank');
        let content = '';

        @if(auth()->user()->role === 3)
        // Parent's print layout
        content = `
            <!DOCTYPE html>
            <html>
            <head>
                <title>Test Report - {{ $test->title }}</title>
                <style>
                    body {
                        font-family: Arial, sans-serif;
                        margin: 20px;
                    }
                    .report-header {
                        text-align: center;
                        margin-bottom: 20px;
                    }
                    .report-title {
                        font-size: 24px;
                        font-weight: bold;
                    }
                    .report-subtitle {
                        font-size: 16px;
                        margin-top: 5px;
                    }
                    .test-info {
                        margin-bottom: 30px;
                        display: grid;
                        grid-template-columns: 1fr 1fr;
                        gap: 20px;
                    }
                    .info-section {
                        background-color: #f8f9fa;
                        padding: 15px;
                        border-radius: 5px;
                    }
                    .info-section h4 {
                        margin-top: 0;
                        color: #495057;
                    }
                    table {
                        width: 100%;
                        border-collapse: collapse;
                        margin-bottom: 30px;
                    }
                    th, td {
                        border: 1px solid #ddd;
                        padding: 8px;
                        text-align: left;
                    }
                    th {
                        background-color: #f2f2f2;
                    }
                    .badge {
                        display: inline-block;
                        padding: 3px 7px;
                        font-size: 12px;
                        font-weight: bold;
                        border-radius: 4px;
                    }
                    .badge-success {
                        background-color: #dff0d8;
                        color: #3c763d;
                        border: 1px solid #3c763d;
                    }
                    .badge-danger {
                        background-color: #f2dede;
                        color: #a94442;
                        border: 1px solid #a94442;
                    }
                    @media print {
                        @page {
                            size: portrait;
                            margin: 1.5cm;
                        }
                    }
                </style>
            </head>
            <body>
                <div class="report-header">
                    <div class="report-title">Test Report</div>
                    <div class="report-subtitle">{{ $test->title }}</div>
                </div>

                <div class="test-info">
                    <div class="info-section">
                        <h4>Test Information</h4>
                        <p><strong>Class:</strong> {{ $test->class->name }}</p>
                        <p><strong>Subject:</strong> {{ $test->subject }}</p>
                        <p><strong>Type:</strong> {{ ucfirst($test->type) }}</p>
                        <p><strong>Date:</strong> {{ $test->exam_date->format('M d, Y') }}</p>
                    </div>
                    <div class="info-section">
                        <h4>Test Details</h4>
                        <p><strong>Total Marks:</strong> {{ $test->total_marks }}</p>
                        <p><strong>Passing Marks:</strong> {{ $test->passing_marks }}</p>
                    </div>
                </div>

                <h3>Test Results</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Student Name</th>
                            <th>Roll Number</th>
                            <th>Marks Obtained</th>
                            <th>Percentage</th>
                            <th>Grade</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($test->results as $result)
                            @if($result->student->parent_id === auth()->id())
                            <tr>
                                <td>{{ $result->student->name }}</td>
                                <td>{{ $result->student->roll_number }}</td>
                                <td>{{ $result->marks_obtained }}/{{ $test->total_marks }}</td>
                                <td>{{ number_format($result->percentage, 1) }}%</td>
                                <td>
                                    <span class="badge badge-{{ $result->is_passed ? 'success' : 'danger' }}">
                                        {{ $result->grade }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-{{ $result->is_passed ? 'success' : 'danger' }}">
                                        {{ $result->is_passed ? 'Passed' : 'Failed' }}
                                    </span>
                                </td>
                            </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </body>
            </html>
        `;
        @else
        // Admin/Teacher print layout (existing code)
        content = `
            <!DOCTYPE html>
            <html>
            <head>
                <title>Test Report - {{ $test->title }}</title>
                <style>
                    body {
                        font-family: Arial, sans-serif;
                        margin: 20px;
                    }
                    .report-header {
                        text-align: center;
                        margin-bottom: 20px;
                    }
                    .report-title {
                        font-size: 24px;
                        font-weight: bold;
                    }
                    .report-subtitle {
                        font-size: 16px;
                        margin-top: 5px;
                    }
                    .test-info {
                        margin-bottom: 30px;
                        display: grid;
                        grid-template-columns: 1fr 1fr;
                        gap: 20px;
                    }
                    .info-section {
                        background-color: #f8f9fa;
                        padding: 15px;
                        border-radius: 5px;
                    }
                    .info-section h4 {
                        margin-top: 0;
                        color: #495057;
                    }
                    table {
                        width: 100%;
                        border-collapse: collapse;
                        margin-bottom: 30px;
                    }
                    th, td {
                        border: 1px solid #ddd;
                        padding: 8px;
                        text-align: left;
                    }
                    th {
                        background-color: #f2f2f2;
                    }
                    .badge {
                        display: inline-block;
                        padding: 3px 7px;
                        font-size: 12px;
                        font-weight: bold;
                        border-radius: 4px;
                    }
                    .badge-success {
                        background-color: #dff0d8;
                        color: #3c763d;
                        border: 1px solid #3c763d;
                    }
                    .badge-danger {
                        background-color: #f2dede;
                        color: #a94442;
                        border: 1px solid #a94442;
                    }
                    .performance-summary {
                        display: grid;
                        grid-template-columns: repeat(4, 1fr);
                        gap: 15px;
                        margin-bottom: 30px;
                    }
                    .summary-card {
                        background-color: #f8f9fa;
                        padding: 15px;
                        border-radius: 5px;
                        text-align: center;
                    }
                    .summary-card h5 {
                        margin: 0 0 10px 0;
                        color: #495057;
                    }
                    .summary-card p {
                        margin: 0;
                        font-size: 20px;
                        font-weight: bold;
                    }
                    @media print {
                        @page {
                            size: portrait;
                            margin: 1.5cm;
                        }
                    }
                </style>
            </head>
            <body>
                <div class="report-header">
                    <div class="report-title">Test Report</div>
                    <div class="report-subtitle">{{ $test->title }}</div>
                </div>

                <div class="test-info">
                    <div class="info-section">
                        <h4>Test Information</h4>
                        <p><strong>Class:</strong> {{ $test->class->name }}</p>
                        <p><strong>Subject:</strong> {{ $test->subject }}</p>
                        <p><strong>Type:</strong> {{ ucfirst($test->type) }}</p>
                        <p><strong>Date:</strong> {{ $test->exam_date->format('M d, Y') }}</p>
                    </div>
                    <div class="info-section">
                        <h4>Test Details</h4>
                        <p><strong>Duration:</strong> {{ $test->duration }} minutes</p>
                        <p><strong>Total Marks:</strong> {{ $test->total_marks }}</p>
                        <p><strong>Passing Marks:</strong> {{ $test->passing_marks }}</p>
                    </div>
                </div>

                <div class="performance-summary">
                    <div class="summary-card">
                        <h5>Total Students</h5>
                        <p>{{ $test->results->count() }}</p>
                    </div>
                    <div class="summary-card">
                        <h5>Pass Rate</h5>
                        <p>
                            @php
                                $passRate = $totalResults > 0 ? ($test->results->where('is_passed', true)->count() / $totalResults) * 100 : 0;
                            @endphp
                            {{ number_format($passRate, 1) }}%
                        </p>
                    </div>
                    <div class="summary-card">
                        <h5>Average Score</h5>
                        <p>
                            @php
                                $avgScore = $totalResults > 0 ? $test->results->avg('percentage') : 0;
                            @endphp
                            {{ number_format($avgScore, 1) }}%
                        </p>
                    </div>
                    <div class="summary-card">
                        <h5>Highest Score</h5>
                        <p>
                            @php
                                $highestScore = $totalResults > 0 ? $test->results->max('percentage') : 0;
                            @endphp
                            {{ number_format($highestScore, 1) }}%
                        </p>
                    </div>
                </div>

                <h3>Test Results</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Student Name</th>
                            <th>Roll Number</th>
                            <th>Marks Obtained</th>
                            <th>Percentage</th>
                            <th>Grade</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($test->results as $result)
                        <tr>
                            <td>{{ $result->student->name }}</td>
                            <td>{{ $result->student->roll_number }}</td>
                            <td>{{ $result->marks_obtained }}/{{ $test->total_marks }}</td>
                            <td>{{ number_format($result->percentage, 1) }}%</td>
                            <td>
                                <span class="badge badge-{{ $result->is_passed ? 'success' : 'danger' }}">
                                    {{ $result->grade }}
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-{{ $result->is_passed ? 'success' : 'danger' }}">
                                    {{ $result->is_passed ? 'Passed' : 'Failed' }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <h3>Grade Distribution</h3>
                @foreach($grades as $grade)
                @php
                $count = $test->results->where('grade', $grade)->count();
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
            </body>
            </html>
        `;
        @endif

        // Write to the new window and print
        printWindow.document.open();
        printWindow.document.write(content);
        printWindow.document.close();

        // Wait for content to load before printing
        printWindow.onload = function() {
            printWindow.print();
            // printWindow.close();
        };
    }

    $(document).ready(function() {
        // Initialize DataTable
        $('#test-results-table').DataTable({
            "pageLength": 25,
            "order": [[2, "desc"]], // Sort by marks obtained by default
            "responsive": true
        });
    });
</script>


@push('styles')
<style>
    @media print {
        /* Hide DataTable controls when printing */
        .dataTables_length,
        .dataTables_filter,
        .dataTables_info,
        .dataTables_paginate,
        .dt-buttons,
        .buttons-copy,
        .buttons-excel,
        .buttons-pdf {
            display: none !important;
        }

        /* Hide other non-essential elements */
        .card-header,
        .btn,
        .collapse-toggle,
        .no-print {
            display: none !important;
        }

        /* Ensure tables take full width */
        .table-responsive {
            overflow: visible !important;
            width: 100% !important;
        }

        .table {
            width: 100% !important;
            margin-bottom: 20px !important;
        }

        /* Remove any fixed table layouts */
        .table {
            table-layout: auto !important;
        }

        /* Ensure good spacing */
        .table th,
        .table td {
            padding: 8px !important;
        }

        /* Page breaks */
        .page-break-before {
            page-break-before: always;
        }

        .page-break-after {
            page-break-after: always;
        }
    }
</style>
@endpush
@endsection
