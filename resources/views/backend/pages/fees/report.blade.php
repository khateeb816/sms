@extends('backend.layouts.app')

@section('title', 'Fees & Fines Report')

@section('content')
<div class="row pt-3">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">
                    Fees & Fines Report ({{ date('M d, Y', strtotime($startDate)) }} - {{ date('M d, Y',
                    strtotime($endDate)) }})
                </h4>
                <div>
                    <button onclick="printTables()" class="btn btn-primary">
                        <i class="fa fa-print mr-1"></i> Print Report
                    </button>
                    <a href="/dash/fees" class="btn btn-secondary">
                        <i class="fa fa-arrow-left mr-1"></i> Back to Dashboard
                    </a>
                </div>
            </div>
            <div class="card-body">
                <!-- Report Filters -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="card border-primary">
                            <div class="card-header bg-light">
                                <h5 class="card-title text-primary"><i class="fa fa-filter mr-2"></i>Report Filters</h5>
                            </div>
                            <div class="card-body">
                                <form action="/dash/public-report" method="GET" id="reportFilterForm">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="student_search">Student (Name/ID/Roll Number)</label>
                                                <input type="text" class="form-control" id="student_search"
                                                    name="student_search" value="{{ $studentSearch ?? '' }}"
                                                    placeholder="Enter student name, ID or roll number">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="start_date">Start Date</label>
                                                <input type="date" class="form-control" id="start_date"
                                                    name="start_date" value="{{ $startDate }}">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="end_date">End Date</label>
                                                <input type="date" class="form-control" id="end_date" name="end_date"
                                                    value="{{ $endDate }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="report_type">Report Type</label>
                                                <select class="form-control" id="report_type" name="report_type">
                                                    <option value="both" {{ $reportType=='both' ? 'selected' : '' }}>
                                                        Both Fees & Fines</option>
                                                    <option value="fees" {{ $reportType=='fees' ? 'selected' : '' }}>
                                                        Fees Only</option>
                                                    <option value="fines" {{ $reportType=='fines' ? 'selected' : '' }}>
                                                        Fines Only</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="fee_status">Fee Status</label>
                                                <select class="form-control" id="fee_status" name="fee_status">
                                                    <option value="all" {{ isset($feeStatus) && $feeStatus=='all'
                                                        ? 'selected' : '' }}>All Statuses</option>
                                                    <option value="paid" {{ isset($feeStatus) && $feeStatus=='paid'
                                                        ? 'selected' : '' }}>Paid</option>
                                                    <option value="pending" {{ isset($feeStatus) &&
                                                        $feeStatus=='pending' ? 'selected' : '' }}>Pending</option>
                                                    <option value="overdue" {{ isset($feeStatus) &&
                                                        $feeStatus=='overdue' ? 'selected' : '' }}>Overdue</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="fine_status">Fine Status</label>
                                                <select class="form-control" id="fine_status" name="fine_status">
                                                    <option value="all" {{ isset($fineStatus) && $fineStatus=='all'
                                                        ? 'selected' : '' }}>All Statuses</option>
                                                    <option value="paid" {{ isset($fineStatus) && $fineStatus=='paid'
                                                        ? 'selected' : '' }}>Paid</option>
                                                    <option value="pending" {{ isset($fineStatus) &&
                                                        $fineStatus=='pending' ? 'selected' : '' }}>Pending</option>
                                                    <option value="waived" {{ isset($fineStatus) &&
                                                        $fineStatus=='waived' ? 'selected' : '' }}>Waived</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>&nbsp;</label>
                                                <button type="submit" class="btn btn-primary btn-block">
                                                    <i class="fa fa-filter mr-1"></i> Apply Filters
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="alert alert-info flex flex-column w-100">
                            <h5>Report Summary</h5>
                            <p>
                                @if(!empty($studentSearch))
                                <strong>Student Filter:</strong> "{{ $studentSearch }}"<br>
                                @endif
                                <strong>Report Type:</strong>
                                @if($reportType == 'fees')
                                Fees Only
                                @elseif($reportType == 'fines')
                                Fines Only
                                @else
                                Both Fees & Fines
                                @endif
                                <br>
                                <strong>Date Range:</strong> {{ date('M d, Y', strtotime($startDate)) }} to {{ date('M
                                d, Y', strtotime($endDate)) }}
                                <br>
                                @if($reportType == 'fees' || $reportType == 'both')
                                <strong>Total Fees:</strong> PKR {{ number_format($fees->sum('amount'), 2) }}
                                (Paid: PKR {{ number_format($fees->where('status', 'paid')->sum('amount'), 2) }},
                                Pending: PKR {{ number_format($fees->where('status', 'pending')->sum('amount'), 2) }},
                                Overdue: PKR {{ number_format($fees->where('status', 'overdue')->sum('amount'), 2) }})
                                <br>
                                <strong>Fee Status Filter:</strong>
                                @if(isset($feeStatus) && $feeStatus != 'all')
                                <span class="badge badge-info">{{ ucfirst($feeStatus) }}</span>
                                @else
                                <span class="badge badge-secondary">All</span>
                                @endif
                                <br>
                                @endif
                                @if($reportType == 'fines' || $reportType == 'both')
                                <strong>Total Fines:</strong> PKR {{ number_format($fines->sum('amount'), 2) }}
                                (Paid: PKR {{ number_format($fines->where('status', 'paid')->sum('amount'), 2) }},
                                Pending: PKR {{ number_format($fines->where('status', 'pending')->sum('amount'), 2) }},
                                Waived: PKR {{ number_format($fines->where('status', 'waived')->sum('amount'), 2) }})
                                <br>
                                <strong>Fine Status Filter:</strong>
                                @if(isset($fineStatus) && $fineStatus != 'all')
                                <span class="badge badge-info">{{ ucfirst($fineStatus) }}</span>
                                @else
                                <span class="badge badge-secondary">All</span>
                                @endif
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                @if($reportType == 'fees' || $reportType == 'both')
                <div class="row mb-4">
                    <div class="col-md-12">
                        <h5>Fees</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Student</th>
                                        <th>Fee Type</th>
                                        <th>Amount</th>
                                        <th>Due Date</th>
                                        <th>Status</th>
                                        <th>Created Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if($fees->count() > 0)
                                    @foreach($fees as $fee)
                                    <tr>
                                        <td>{{ $fee->id }}</td>
                                        <td>{{ $fee->student->name ?? 'N/A' }}</td>
                                        <td>{{ $fee->fee_type }}</td>
                                        <td>PKR {{ number_format($fee->amount, 2) }}</td>
                                        <td>{{ $fee->due_date->format('M d, Y') }}</td>
                                        <td>
                                            @if($fee->status == 'paid')
                                            <span class="badge badge-success">Paid</span>
                                            @elseif($fee->status == 'pending')
                                            <span class="badge badge-warning">Pending</span>
                                            @elseif($fee->status == 'overdue')
                                            <span class="badge badge-danger">Overdue</span>
                                            @endif
                                        </td>
                                        <td>{{ $fee->created_at->format('M d, Y') }}</td>
                                    </tr>
                                    @endforeach
                                    @else
                                    <tr>
                                        <td colspan="7" class="text-center">No fees found for the selected date range.
                                        </td>
                                    </tr>
                                    @endif
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="3" class="text-right">Total:</th>
                                        <th>PKR {{ number_format($fees->sum('amount'), 2) }}</th>
                                        <th colspan="3"></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
                @endif

                @if($reportType == 'fines' || $reportType == 'both')
                <div class="row">
                    <div class="col-md-12">
                        <h5>Fines</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Student</th>
                                        <th>Fine Type</th>
                                        <th>Amount</th>
                                        <th>Issue Date</th>
                                        <th>Status</th>
                                        <th>Created Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if($fines->count() > 0)
                                    @foreach($fines as $fine)
                                    <tr>
                                        <td>{{ $fine->id }}</td>
                                        <td>{{ $fine->student->name ?? 'N/A' }}</td>
                                        <td>{{ $fine->fine_type }}</td>
                                        <td>PKR {{ number_format($fine->amount, 2) }}</td>
                                        <td>{{ $fine->issue_date->format('M d, Y') }}</td>
                                        <td>
                                            @if($fine->status == 'paid')
                                            <span class="badge badge-success">Paid</span>
                                            @elseif($fine->status == 'pending')
                                            <span class="badge badge-warning">Pending</span>
                                            @elseif($fine->status == 'waived')
                                            <span class="badge badge-info">Waived</span>
                                            @endif
                                        </td>
                                        <td>{{ $fine->created_at->format('M d, Y') }}</td>
                                    </tr>
                                    @endforeach
                                    @else
                                    <tr>
                                        <td colspan="7" class="text-center">No fines found for the selected date range.
                                        </td>
                                    </tr>
                                    @endif
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="3" class="text-right">Total:</th>
                                        <th>PKR {{ number_format($fines->sum('amount'), 2) }}</th>
                                        <th colspan="3"></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
                @endif
            </div>
            <div class="card-footer text-muted">
                <p>Report generated on {{ date('F d, Y h:i A') }}</p>
            </div>
        </div>
    </div>
</div>
<script>
    function printTables() {
        // Create a new window for printing
        let printWindow = window.open('', '_blank');
        
        // Get all table-responsive elements
        let tables = document.querySelectorAll('.table-responsive');
        
        // Create content with just the tables
        let content = `
            <!DOCTYPE html>
            <html>
            <head>
                <title>Fee and Fine Report</title>
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
                    <div class="report-title">Fee and Fine Report</div>
                    <div class="report-subtitle">
                        Period: ${document.getElementById('start_date').value || '{{ $startDate }}'} to ${document.getElementById('end_date').value || '{{ $endDate }}'}
                    </div>
                    <div class="report-subtitle">
                        Report Type: ${document.getElementById('report_type') ? document.getElementById('report_type').options[document.getElementById('report_type').selectedIndex].text : '{{ ucfirst($reportType) }}'}
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
            
            // Add the cleaned table to the content
            content += `<div class="table-title">${tableClone.previousElementSibling ? tableClone.previousElementSibling.textContent : ''}</div>`;
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

    document.addEventListener('DOMContentLoaded', function() {
        // Initialize DataTables for all tables
        const tables = document.querySelectorAll('.table');
        tables.forEach(table => {
            $(table).DataTable({
                responsive: true,
                autoWidth: false,
                pageLength: 10,
                lengthMenu: [10, 25, 50, 100],
                language: {
                    lengthMenu: "Show _MENU_ entries",
                    zeroRecords: "No records found", 
                    info: "Showing page _PAGE_ of _PAGES_",
                    infoEmpty: "No records available",
                    infoFiltered: "(filtered from _MAX_ total records)"
                },
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'excel', 'pdf',
                    {
                        text: 'Print',
                        action: function (e, dt, node, config) {
                            printTables();
                        }
                    }
                ]
            });
        });

        // Show/hide report type specific filters
        const reportTypeSelect = document.getElementById('report_type');
        const feeStatus = document.getElementById('fee_status');
        const fineStatus = document.getElementById('fine_status');

        reportTypeSelect.addEventListener('change', function() {
            const reportType = this.value;
            
            if (reportType === 'fees') {
                feeStatus.closest('.col-md-4').style.display = 'block';
                fineStatus.closest('.col-md-4').style.display = 'none';
            } else if (reportType === 'fines') {
                feeStatus.closest('.col-md-4').style.display = 'none';
                fineStatus.closest('.col-md-4').style.display = 'block';
            } else {
                feeStatus.closest('.col-md-4').style.display = 'block';
                fineStatus.closest('.col-md-4').style.display = 'block';
            }
        });

        // Trigger the change event on page load
        reportTypeSelect.dispatchEvent(new Event('change'));
    });
</script>
@endsection

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

        /* Show only tables and their content */
        body * {
            visibility: hidden;
        }

        .table-responsive,
        .table-responsive * {
            visibility: visible;
        }

        .table-responsive {
            position: absolute;
            left: 0;
            top: 0;
        }

        /* Add some spacing between tables */
        .table-responsive+.table-responsive {
            margin-top: 50px;
        }
    }
</style>
@endpush