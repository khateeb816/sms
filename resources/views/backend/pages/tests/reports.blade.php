@extends('backend.layouts.app')

@section('title', 'Test Reports')

@section('content')
<div class=" ">
    <div class="container-fluid">
        <!-- Breadcrumb-->
        <div class="row pt-2 pb-2">
            <div class="col-sm-9">
                <h4 class="page-title">Test Reports</h4>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('tests.index') }}">Tests</a></li>
                    <li class="breadcrumb-item active">Reports</li>
                </ol>
            </div>
            <div class="col-sm-3">
                <div class="btn-group float-sm-right">
                    <button type="button" class="btn btn-primary waves-effect waves-light" onclick="printReport()">
                        <i class="fas fa-print"></i> Print Report
                    </button>
                </div>
            </div>
        </div>
        <!-- End Breadcrumb-->

        <!-- Filters -->
        <div class="row">
            <div class="col-12">
                <div class="card border-primary">
                    <div class="card-header bg-light">
                        <h5 class="card-title text-primary"><i class="fa fa-filter mr-2"></i>Report Filters</h5>
                    </div>
                    <div class="card-body">
                        <form id="filterForm" method="GET" action="{{ route('test.reports') }}">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Class</label>
                                        <select name="class_id" class="form-control">
                                            <option value="">All Classes</option>
                                            @foreach($classes as $class)
                                                <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                                                    {{ $class->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Test Type</label>
                                        <select name="type" class="form-control">
                                            <option value="">All Types</option>
                                            <option value="normal" {{ request('type') == 'normal' ? 'selected' : '' }}>Normal</option>
                                            <option value="weekly" {{ request('type') == 'weekly' ? 'selected' : '' }}>Weekly</option>
                                            <option value="monthly" {{ request('type') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                            <option value="yearly" {{ request('type') == 'yearly' ? 'selected' : '' }}>Yearly</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Start Date</label>
                                        <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}" placeholder="Start Date">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>End Date</label>
                                        <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}" placeholder="End Date">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fa fa-filter mr-1"></i> Apply Filters
                                    </button>
                                    <a href="{{ route('test.reports') }}" class="btn btn-secondary">
                                        <i class="fa fa-undo mr-1"></i> Reset
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Test Results Table -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Completed Tests</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="test-reports-table" class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Test Title</th>
                                        <th>Class</th>
                                        <th>Subject</th>
                                        <th>Type</th>
                                        <th>Date</th>
                                        <th>Total Students</th>
                                        <th>Average Score</th>
                                        <th>Pass Rate</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($tests as $test)
                                    <tr>
                                        <td>{{ $test->title }}</td>
                                        <td>{{ $test->class->name }}</td>
                                        <td>{{ $test->subject }}</td>
                                        <td>
                                            <span class="badge badge-pill badge-info text-uppercase">{{ $test->type }}</span>
                                        </td>
                                        <td>{{ $test->exam_date->format('M d, Y') }}</td>
                                        <td>{{ $test->results->count() }}</td>
                                        <td>{{ number_format($test->results->avg('percentage'), 1) }}%</td>
                                        <td>
                                            @php
                                            $totalResults = $test->results->count();
                                            $passRate = $totalResults > 0 ? ($test->results->where('is_passed', true)->count() / $totalResults) * 100 : 0;
                                            @endphp
                                            {{ number_format($passRate, 1) }}%
                                        </td>
                                        <td>
                                            <a href="{{ route('tests.show', $test) }}" class="btn btn-info btn-sm waves-effect waves-light">
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

        <!-- Summary Statistics -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Performance Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="card bg-primary text-white">
                                    <div class="card-body">
                                        <h5>Total Tests</h5>
                                        <p class="mb-0">{{ $totalTests }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-success text-white">
                                    <div class="card-body">
                                        <h5>Total Students</h5>
                                        <p class="mb-0">{{ $totalStudents }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-info text-white">
                                    <div class="card-body">
                                        <h5>Average Score</h5>
                                        <p class="mb-0">{{ number_format($averageScore, 1) }}%</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-warning text-white">
                                    <div class="card-body">
                                        <h5>Average Pass Rate</h5>
                                        <p class="mb-0">{{ number_format($averagePassRate, 1) }}%</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Subject-wise Performance -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Subject-wise Performance</h5>
                    </div>
                    <div class="card-body">
                        @php
                            $subjectStats = $tests->groupBy('subject')->map(function($subjectTests) {
                                $totalTests = $subjectTests->count();
                                $totalResults = $subjectTests->flatMap->results;
                                $totalCount = $totalResults->count();
                                $avgScore = $totalResults->avg('percentage') ?? 0;
                                $passRate = $totalCount > 0 ? ($totalResults->where('is_passed', true)->count() / $totalCount) * 100 : 0;

                                return [
                                    'total_tests' => $totalTests,
                                    'avg_score' => $avgScore,
                                    'pass_rate' => $passRate
                                ];
                            });
                        @endphp
                        <div class="row mb-2">
                            <div class="col-md-2">
                                <small class="text-muted">Subject</small>
                            </div>
                            <div class="col-md-5">
                                <small class="text-muted">Average Score</small>
                            </div>
                            <div class="col-md-5">
                                <small class="text-muted">Pass Rate</small>
                            </div>
                        </div>
                        @foreach($subjectStats as $subject => $stats)
                        <div class="mb-2">
                            <div class="row align-items-center">
                                <div class="col-md-2">
                                    <div class="d-flex align-items-center">
                                        <h6 class="mb-0">{{ $subject }}</h6>
                                        <span class="badge badge-info ml-2">{{ $stats['total_tests'] }}</span>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="d-flex align-items-center">
                                        <div class="progress flex-grow-1" style="height: 20px;">
                                            <div class="progress-bar bg-primary" role="progressbar"
                                                style="width: {{ $stats['avg_score'] }}%"
                                                aria-valuenow="{{ $stats['avg_score'] }}"
                                                aria-valuemin="0"
                                                aria-valuemax="100">
                                            </div>
                                        </div>
                                        <span class="ml-2">{{ number_format($stats['avg_score'], 1) }}%</span>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="d-flex align-items-center">
                                        <div class="progress flex-grow-1" style="height: 20px;">
                                            <div class="progress-bar bg-success" role="progressbar"
                                                style="width: {{ $stats['pass_rate'] }}%"
                                                aria-valuenow="{{ $stats['pass_rate'] }}"
                                                aria-valuemin="0"
                                                aria-valuemax="100">
                                            </div>
                                        </div>
                                        <span class="ml-2">{{ number_format($stats['pass_rate'], 1) }}%</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Performance Analysis -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Performance Trends</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="performanceChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    function printReport() {
        // Create a new window for printing
        let printWindow = window.open('', '_blank');

        // Get all table-responsive elements
        let tables = document.querySelectorAll('.table-responsive');

        // Create content with just the tables
        let content = `
            <!DOCTYPE html>
            <html>
            <head>
                <title>Test Reports</title>
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
                    .table-title {
                        font-size: 18px;
                        font-weight: bold;
                        margin: 20px 0 10px 0;
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
                    .badge-warning {
                        background-color: #fcf8e3;
                        color: #8a6d3b;
                        border: 1px solid #8a6d3b;
                    }
                    .badge-info {
                        background-color: #d9edf7;
                        color: #31708f;
                        border: 1px solid #31708f;
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
                    <div class="report-title">Test Reports</div>
                    <div class="report-subtitle">
                        Period: ${document.getElementById('start_date') ? document.getElementById('start_date').value : ''} to ${document.getElementById('end_date') ? document.getElementById('end_date').value : ''}
                    </div>
                    <div class="report-subtitle">
                        Class: ${document.querySelector('select[name="class_id"]') ? document.querySelector('select[name="class_id"]').options[document.querySelector('select[name="class_id"]').selectedIndex].text : 'All Classes'}
                    </div>
                    <div class="report-subtitle">
                        Test Type: ${document.querySelector('select[name="type"]') ? document.querySelector('select[name="type"]').options[document.querySelector('select[name="type"]').selectedIndex].text : 'All Types'}
                    </div>
                </div>
        `;

        // Add each table to the content
        tables.forEach(function(tableContainer) {
            // Clone the table to avoid modifying the original
            let tableClone = tableContainer.cloneNode(true);

            // Remove any DataTables specific elements
            let dataTableElements = tableClone.querySelectorAll('.dataTables_length, .dataTables_filter, .dataTables_info, .dataTables_paginate, .dt-buttons');
            dataTableElements.forEach(function(element) {
                element.parentNode.removeChild(element);
            });

            // Remove the actions column
            let table = tableClone.querySelector('table');
            if (table) {
                let rows = table.querySelectorAll('tr');
                rows.forEach(function(row) {
                    let cells = row.querySelectorAll('th, td');
                    if (cells.length > 0) {
                        // Remove the last cell (actions column)
                        cells[cells.length - 1].remove();
                    }
                });
            }

            // Add the cleaned table to the content
            content += `<div class="table-title">${tableContainer.previousElementSibling ? tableContainer.previousElementSibling.textContent : ''}</div>`;
            content += tableClone.outerHTML;
        });

        // Close the HTML structure
        content += `
            </body>
            </html>
        `;

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
        // Initialize DataTable with custom options
        $('#test-reports-table').DataTable({
            "pageLength": 25,
            "order": [[4, "desc"]], // Sort by date by default
            "responsive": true,
            "dom": 'Bfrtip',
            "buttons": [
                'copy', 'csv', 'excel', 'pdf',
                {
                    text: 'Print',
                    action: function (e, dt, node, config) {
                        printReport();
                    }
                }
            ]
        });

        // Initialize Performance Chart
        var ctx = document.getElementById('performanceChart').getContext('2d');
        var performanceData = {!! json_encode($tests->map(function($test) {
            $totalResults = $test->results->count();
            $avgScore = $totalResults > 0 ? $test->results->avg('percentage') : 0;
            $passRate = $totalResults > 0 ? ($test->results->where('is_passed', true)->count() / $totalResults) * 100 : 0;

            return [
                'date' => $test->exam_date->format('M d'),
                'average' => $avgScore,
                'passRate' => $passRate
            ];
        })) !!};

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: performanceData.map(item => item.date),
                datasets: [{
                    label: 'Average Score (%)',
                    data: performanceData.map(item => item.average),
                    backgroundColor: 'rgba(75, 192, 192, 0.5)',
                    borderColor: 'rgb(75, 192, 192)',
                    borderWidth: 1,
                    borderRadius: 5,
                    barPercentage: 0.8,
                    categoryPercentage: 0.4
                }, {
                    label: 'Pass Rate (%)',
                    data: performanceData.map(item => item.passRate),
                    backgroundColor: 'rgba(255, 99, 132, 0.5)',
                    borderColor: 'rgb(255, 99, 132)',
                    borderWidth: 1,
                    borderRadius: 5,
                    barPercentage: 0.8,
                    categoryPercentage: 0.4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Performance Trends by Test Date'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        grid: {
                            drawBorder: false
                        },
                        ticks: {
                            callback: function(value) {
                                return value + '%';
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
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
