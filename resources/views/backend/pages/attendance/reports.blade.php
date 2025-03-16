@extends('backend.layouts.app')

@section('title', 'Attendance Reports')

@section('content')
<div class="row pt-3">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">Attendance Reports</h4>
                <div class="no-print">
                    @if($type == 'student')
                    <a href="{{ route('attendance.students.index') }}" class="btn btn-primary btn-sm">
                        <i class="fa fa-arrow-left mr-1"></i> Back to Student Attendance
                    </a>
                    @else
                    <a href="{{ route('attendance.teachers.index') }}" class="btn btn-primary btn-sm">
                        <i class="fa fa-arrow-left mr-1"></i>
                        @if(auth()->user()->role == 2)
                        Back to My Attendance
                        @else
                        Back to Teacher Attendance
                        @endif
                    </a>
                    @endif
                    <button onclick="printAttendanceReport()" class="btn btn-info btn-sm ml-2">
                        <i class="fa fa-print mr-1"></i> Print Report
                    </button>
                </div>
            </div>
            <div class="card-body">
                <!-- Filters -->
                <div class="row mb-4 no-print">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header bg-light">
                                <h5 class="card-title">
                                    <i class="fa fa-filter mr-2"></i>Filter Reports
                                </h5>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('attendance.reports') }}" method="GET" id="filterForm">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="type">Attendance Type</label>
                                                <select class="form-control" id="type" name="type"
                                                    onchange="toggleFilters()">
                                                    @if(auth()->user()->role == 2)
                                                    <option value="teacher" {{ $type=='teacher' ? 'selected' : '' }}>
                                                        My Attendance
                                                    </option>
                                                    <option value="student" {{ $type=='student' ? 'selected' : '' }}>
                                                        Student Attendance
                                                    </option>
                                                    @else
                                                    <option value="student" {{ $type=='student' ? 'selected' : '' }}>
                                                        Student Attendance
                                                    </option>
                                                    <option value="teacher" {{ $type=='teacher' ? 'selected' : '' }}>
                                                        Teacher Attendance
                                                    </option>
                                                    @endif
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-3" id="classFilterDiv"
                                            style="{{ $type == 'teacher' ? 'display:none;' : '' }}">
                                            <div class="form-group">
                                                <label for="class_id">Class</label>
                                                <select class="form-control" id="class_id" name="class_id">
                                                    <option value="">All Classes</option>
                                                    @foreach($classes as $class)
                                                    <option value="{{ $class->id }}" {{ request('class_id')==$class->id
                                                        ? 'selected' : '' }}>
                                                        {{ $class->name }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="user_id" id="userLabel">
                                                    @if($type == 'student')
                                                    Student
                                                    @else
                                                    @if(auth()->user()->role == 2)
                                                    My Record
                                                    @else
                                                    Teacher
                                                    @endif
                                                    @endif
                                                </label>
                                                <select class="form-control" id="user_id" name="user_id" {{
                                                    auth()->user()->role == 2 && $type == 'teacher' ? 'disabled' : ''
                                                    }}>
                                                    <option value="">
                                                        @if($type == 'student')
                                                        All Students
                                                        @else
                                                        @if(auth()->user()->role == 2)
                                                        My Records
                                                        @else
                                                        All Teachers
                                                        @endif
                                                        @endif
                                                    </option>
                                                    @foreach($users as $user)
                                                    <option value="{{ $user->id }}" {{ request('user_id')==$user->id ?
                                                        'selected' : '' }}>
                                                        {{ $user->name }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="status">Status</label>
                                                <select class="form-control" id="status" name="status">
                                                    <option value="">All Statuses</option>
                                                    <option value="present" {{ request('status')=='present' ? 'selected'
                                                        : '' }}>Present</option>
                                                    <option value="absent" {{ request('status')=='absent' ? 'selected'
                                                        : '' }}>Absent</option>
                                                    <option value="late" {{ request('status')=='late' ? 'selected' : ''
                                                        }}>Late</option>
                                                    <option value="leave" {{ request('status')=='leave' ? 'selected'
                                                        : '' }}>Leave</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="start_date">Start Date</label>
                                                <input type="date" class="form-control" id="start_date"
                                                    name="start_date"
                                                    value="{{ request('start_date', $startDate->format('Y-m-d')) }}">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="end_date">End Date</label>
                                                <input type="date" class="form-control" id="end_date" name="end_date"
                                                    value="{{ request('end_date', $endDate->format('Y-m-d')) }}">
                                            </div>
                                        </div>
                                        <div class="col-md-12 text-right">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fa fa-search mr-1"></i> Filter Results
                                            </button>
                                            <a href="{{ route('attendance.reports') }}" class="btn btn-secondary ml-2">
                                                <i class="fa fa-redo mr-1"></i> Reset Filters
                                            </a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Report Header for Print -->
                <div class="d-none d-print-block mb-4">
                    <div class="text-center">
                        <h2>{{ config('app.name') }}</h2>
                        <h3>
                            @if($type == 'student')
                            Student Attendance Report
                            @else
                            @if(auth()->user()->role == 2)
                            My Attendance Report
                            @else
                            Teacher Attendance Report
                            @endif
                            @endif
                        </h3>
                        <p>
                            Period: {{ $startDate->format('d M, Y') }} to
                            {{ $endDate->format('d M, Y') }}
                        </p>
                        @if($type == 'student' && request('class_id'))
                        <p>Class: {{ $classes->where('id', request('class_id'))->first()->name ?? 'All Classes' }}</p>
                        @endif
                        @if(request('user_id'))
                        <p>{{ $type == 'student' ? 'Student' : 'Teacher' }}:
                            {{ $users->where('id', request('user_id'))->first()->name ?? 'All' }}
                        </p>
                        @endif
                        @if(request('status'))
                        <p>Status: {{ ucfirst(request('status')) }}</p>
                        @endif
                    </div>
                </div>

                <!-- Report Summary -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header bg-light">
                                <h5 class="card-title">
                                    <i class="fa fa-chart-pie mr-2"></i>Summary
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="card bg-success text-white">
                                            <div class="card-body text-center">
                                                <h5 class="card-title">Present</h5>
                                                <h2>{{ $summary['present'] }}</h2>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="card bg-danger text-white">
                                            <div class="card-body text-center">
                                                <h5 class="card-title">Absent</h5>
                                                <h2>{{ $summary['absent'] }}</h2>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="card bg-warning text-white">
                                            <div class="card-body text-center">
                                                <h5 class="card-title">Late</h5>
                                                <h2>{{ $summary['late'] }}</h2>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="card bg-info text-white">
                                            <div class="card-body text-center">
                                                <h5 class="card-title">Leave</h5>
                                                <h2>{{ $summary['leave'] }}</h2>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Attendance Records -->
                <div class="card">
                    <div class="card-header bg-light">
                        <h5 class="card-title">
                            <i class="fa fa-list mr-2"></i>Attendance Records
                        </h5>
                    </div>
                    <div class="card-body">
                        @if(count($attendances) > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Date</th>
                                        <th>
                                            @if($type == 'student')
                                            Student Name
                                            @else
                                            @if(auth()->user()->role == 2)
                                            My Name
                                            @else
                                            Teacher Name
                                            @endif
                                            @endif
                                        </th>
                                        @if($type == 'student')
                                        <th>Class</th>
                                        @endif
                                        <th>Status</th>
                                        <th>Remarks</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($attendances as $index => $attendance)
                                    <tr class="{{ 
                                        $attendance->status == 'present' ? 'table-success' : 
                                        ($attendance->status == 'absent' ? 'table-danger' : 
                                        ($attendance->status == 'late' ? 'table-warning' : 'table-info')) 
                                    }}">
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $attendance->date->format('d M, Y') }}</td>
                                        <td>{{ $attendance->user->name }}</td>
                                        @if($type == 'student')
                                        <td>{{ $attendance->user->classes->first()->name ?? 'N/A' }}</td>
                                        @endif
                                        <td>{{ ucfirst($attendance->status) }}</td>
                                        <td>{{ $attendance->remarks }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3 no-print">
                            {{ $attendances->links() }}
                        </div>
                        @else
                        <div class="alert alert-info">
                            <i class="fa fa-info-circle mr-1"></i> No attendance records found for the selected
                            criteria.
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    function printAttendanceReport() {
        // Open a new window
        var printWindow = window.open('', '_blank', 'width=800,height=600');
        
        // Get the summary and attendance table
        var summarySection = document.querySelector('.card .card-body .row.mb-4');
        var attendanceTable = document.querySelector('.table-responsive');
        
        // Log the print activity via AJAX
        var xhr = new XMLHttpRequest();
        xhr.open('POST', "{{ route('attendance.log-print') }}", true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        
        var data = '_token=' + encodeURIComponent("{{ csrf_token() }}") + 
                   '&type=' + encodeURIComponent("{{ $type }}") +
                   '&start_date=' + encodeURIComponent("{{ $startDate->format('Y-m-d') }}") +
                   '&end_date=' + encodeURIComponent("{{ $endDate->format('Y-m-d') }}") +
                   '&user_id=' + encodeURIComponent("{{ request('user_id') }}") +
                   '&class_id=' + encodeURIComponent("{{ request('class_id') }}") +
                   '&status=' + encodeURIComponent("{{ request('status') }}");
                   
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4) {
                if (xhr.status === 200) {
                    console.log("Print activity logged successfully");
                } else {
                    console.error("Error logging print activity", xhr.statusText);
                }
            }
        };
        
        xhr.send(data);
        
        // Create the content for the new window
        var content = '<!DOCTYPE html>' +
            '<html>' +
            '<head>' +
                '<title>Attendance Report</title>' +
                '<style>' +
                    'body {' +
                        'font-family: Arial, sans-serif;' +
                        'margin: 0;' +
                        'padding: 20px;' +
                        'color: #000;' +
                    '}' +
                    
                    '.report-header {' +
                        'text-align: center;' +
                        'margin-bottom: 30px;' +
                    '}' +
                    
                    '.report-title {' +
                        'font-size: 24px;' +
                        'font-weight: bold;' +
                        'margin-bottom: 10px;' +
                    '}' +
                    
                    '.report-subtitle {' +
                        'font-size: 16px;' +
                        'margin-bottom: 5px;' +
                    '}' +
                    
                    '.summary-container {' +
                        'display: flex;' +
                        'justify-content: space-between;' +
                        'margin-bottom: 30px;' +
                        'flex-wrap: wrap;' +
                    '}' +
                    
                    '.summary-card {' +
                        'width: 22%;' +
                        'padding: 15px;' +
                        'border-radius: 5px;' +
                        'text-align: center;' +
                        'color: white;' +
                        'margin-bottom: 15px;' +
                    '}' +
                    
                    '.summary-card.present { background-color: #28a745; }' +
                    '.summary-card.absent { background-color: #dc3545; }' +
                    '.summary-card.late { background-color: #ffc107; }' +
                    '.summary-card.leave { background-color: #17a2b8; }' +
                    
                    '.summary-title {' +
                        'font-size: 16px;' +
                        'margin-bottom: 10px;' +
                    '}' +
                    
                    '.summary-value {' +
                        'font-size: 24px;' +
                        'font-weight: bold;' +
                    '}' +
                    
                    '.section-title {' +
                        'font-size: 18px;' +
                        'font-weight: bold;' +
                        'margin-bottom: 15px;' +
                        'border-bottom: 1px solid #000;' +
                        'padding-bottom: 5px;' +
                    '}' +
                    
                    'table {' +
                        'width: 100%;' +
                        'border-collapse: collapse;' +
                        'margin-bottom: 30px;' +
                    '}' +
                    
                    'th, td {' +
                        'border: 1px solid #000;' +
                        'padding: 8px;' +
                        'text-align: left;' +
                    '}' +
                    
                    'th { background-color: #f2f2f2; }' +
                    'tr.present { background-color: #d4edda; }' +
                    'tr.absent { background-color: #f8d7da; }' +
                    'tr.late { background-color: #fff3cd; }' +
                    'tr.leave { background-color: #d1ecf1; }' +
                    
                    '.footer {' +
                        'text-align: right;' +
                        'font-size: 12px;' +
                        'margin-top: 30px;' +
                        'border-top: 1px solid #ddd;' +
                        'padding-top: 10px;' +
                    '}' +
                    
                    '@media print {' +
                        '@page { size: portrait; margin: 1.5cm; }' +
                        'body { padding: 0; }' +
                    '}' +
                '</style>' +
            '</head>' +
            '<body>' +
                '<div class="report-header">' +
                    '<div class="report-title">{{ config("app.name") }}</div>' +
                    '<div class="report-title">{{ $type == "student" ? "Student" : "Teacher" }} Attendance Report</div>' +
                    '<div class="report-subtitle">' +
                        'Period: {{ $startDate->format("d M, Y") }} to {{ $endDate->format("d M, Y") }}' +
                    '</div>';

        if ("{{ $type }}" === 'student' && "{{ request('class_id') }}") {
            content += '<div class="report-subtitle">' +
                'Class: {{ $classes->where("id", request("class_id"))->first()->name ?? "All Classes" }}' +
                '</div>';
        }

        if ("{{ request('user_id') }}") {
            content += '<div class="report-subtitle">' +
                '{{ $type == "student" ? "Student" : "Teacher" }}: ' +
                '{{ $users->where("id", request("user_id"))->first()->name ?? "All" }}' +
                '</div>';
        }

        if ("{{ request('status') }}") {
            content += '<div class="report-subtitle">' +
                'Status: {{ ucfirst(request("status")) }}' +
                '</div>';
        }

        content += '</div>' +
            '<div class="section-title">Summary</div>' +
            '<div class="summary-container">' +
                '<div class="summary-card present">' +
                    '<div class="summary-title">Present</div>' +
                    '<div class="summary-value">{{ $summary["present"] }}</div>' +
                '</div>' +
                '<div class="summary-card absent">' +
                    '<div class="summary-title">Absent</div>' +
                    '<div class="summary-value">{{ $summary["absent"] }}</div>' +
                '</div>' +
                '<div class="summary-card late">' +
                    '<div class="summary-title">Late</div>' +
                    '<div class="summary-value">{{ $summary["late"] }}</div>' +
                '</div>' +
                '<div class="summary-card leave">' +
                    '<div class="summary-title">Leave</div>' +
                    '<div class="summary-value">{{ $summary["leave"] }}</div>' +
                '</div>' +
            '</div>' +
            '<div class="section-title">Attendance Records</div>';

        if (attendanceTable) {
            var table = attendanceTable.querySelector('table');
            if (table) {
                var tableClone = table.cloneNode(true);
                var rows = tableClone.querySelectorAll('tbody tr');
                
                rows.forEach(function(row) {
                    var statusCell = row.querySelector('td:nth-child({{ $type == "student" ? "5" : "4" }})');
                    if (statusCell) {
                        var status = statusCell.textContent.trim().toLowerCase();
                        row.className = status;
                    }
                });
                
                content += tableClone.outerHTML;
            } else {
                content += '<p>No attendance records found.</p>';
            }
        } else {
            content += '<p>No attendance records found.</p>';
        }

        content += '<div class="footer">' +
                'Generated on: ' + new Date().toLocaleString() +
            '</div>' +
            '</body>' +
            '</html>';

        printWindow.document.open();
        printWindow.document.write(content);
        printWindow.document.close();

        printWindow.onload = function() {
            printWindow.print();
        };
    }

    function toggleFilters() {
        var attendanceType = document.getElementById('type').value;
        var classFilterDiv = document.getElementById('classFilterDiv');
        var userLabel = document.getElementById('userLabel');
        var userSelect = document.getElementById('user_id');
        var isTeacher = {{ auth()->user()->role == 2 ? 'true' : 'false' }};
        
        if (attendanceType === 'student') {
            classFilterDiv.style.display = 'block';
            userLabel.textContent = 'Student';
            userSelect.options[0].text = 'All Students';
            userSelect.disabled = false;
        } else {
            classFilterDiv.style.display = 'none';
            if (isTeacher) {
                userLabel.textContent = 'My Record';
                userSelect.options[0].text = 'My Records';
                userSelect.disabled = true;
                // Force select the teacher's own record
                @if(auth()->user()->role == 2)
                userSelect.value = "{{ auth()->id() }}";
                @endif
            } else {
                userLabel.textContent = 'Teacher';
                userSelect.options[0].text = 'All Teachers';
                userSelect.disabled = false;
            }
        }
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        toggleFilters();
    });
</script>
@endsection

@push('styles')
<style>
    /* Style for when printing is active */
    body.printing-attendance-report .sidebar-wrapper,
    body.printing-attendance-report .left-sidebar,
    body.printing-attendance-report .topbar {
        display: none !important;
    }

    body.printing-attendance-report .page-wrapper {
        margin-left: 0 !important;
        padding-left: 0 !important;
    }

    @media print {

        /* Page setup */
        @page {
            size: portrait;
            margin: 1.5cm;
        }

        /* Hide navigation elements */
        .no-print,
        .topbar,
        .left-sidebar,
        .right-sidebar,
        .page-footer,
        .sidebar-menu,
        .sidebar-wrapper,
        .sidebar-header,
        .sidebar-footer,
        .mobile-topbar,
        .page-breadcrumb,
        nav,
        footer,
        .card-action,
        .dropdown,
        .btn,
        .card-header button {
            display: none !important;
        }

        /* Show only essential elements */
        .d-print-block {
            display: block !important;
        }

        /* Adjust layout for printing */
        body {
            font-size: 12pt;
            line-height: 1.3;
            background: #fff !important;
            color: #000 !important;
        }

        .content-wrapper,
        .page-wrapper,
        .page-content,
        .content-page,
        body,
        html {
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
            max-width: 100% !important;
            min-width: 100% !important;
            background-color: white !important;
        }

        /* Force the page wrapper to take full width */
        .page-wrapper {
            margin-left: 0 !important;
            transition: none !important;
        }

        /* Remove borders and shadows */
        .card {
            border: none !important;
            box-shadow: none !important;
            margin-bottom: 1.5rem !important;
            page-break-inside: avoid;
        }

        .card-header {
            background-color: transparent !important;
            color: #000 !important;
            border-bottom: 1px solid #000 !important;
            font-weight: bold;
            padding: 0.5rem 0 !important;
            margin-bottom: 1rem !important;
        }

        .card-body {
            padding: 0 !important;
        }

        /* Ensure good table formatting in print */
        .table-responsive {
            overflow: visible !important;
            width: 100% !important;
        }

        .table {
            width: 100% !important;
            margin-bottom: 1.5rem !important;
            color: #000 !important;
            border-collapse: collapse !important;
            page-break-inside: auto !important;
        }

        .table thead {
            display: table-header-group;
        }

        .table tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }

        .table th,
        .table td {
            padding: 0.5rem !important;
            border: 1px solid #000 !important;
            font-size: 10pt !important;
            page-break-inside: avoid;
        }

        /* Ensure background colors print properly */
        .table-success {
            background-color: #d4edda !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .table-danger {
            background-color: #f8d7da !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .table-warning {
            background-color: #fff3cd !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .table-info {
            background-color: #d1ecf1 !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        /* Summary cards in print */
        .row {
            display: flex !important;
            flex-wrap: wrap !important;
            margin-right: -15px !important;
            margin-left: -15px !important;
        }

        .col-md-3 {
            flex: 0 0 25% !important;
            max-width: 25% !important;
            padding-right: 15px !important;
            padding-left: 15px !important;
        }

        .bg-success,
        .bg-danger,
        .bg-warning,
        .bg-info {
            padding: 0.5rem !important;
            margin-bottom: 1rem !important;
            border-radius: 0.25rem !important;
            text-align: center !important;
            page-break-inside: avoid !important;
        }

        .bg-success {
            background-color: #28a745 !important;
            color: white !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .bg-danger {
            background-color: #dc3545 !important;
            color: white !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .bg-warning {
            background-color: #ffc107 !important;
            color: white !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .bg-info {
            background-color: #17a2b8 !important;
            color: white !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        /* Report header styling */
        .d-print-block {
            margin-bottom: 2rem !important;
        }

        .d-print-block h2 {
            font-size: 18pt !important;
            margin-bottom: 0.5rem !important;
            font-weight: bold !important;
        }

        .d-print-block h3 {
            font-size: 16pt !important;
            margin-bottom: 0.5rem !important;
        }

        .d-print-block p {
            font-size: 12pt !important;
            margin-bottom: 0.25rem !important;
        }

        /* Adjust the main content area to take full width */
        .col-lg-12 {
            width: 100% !important;
            max-width: 100% !important;
            flex: 0 0 100% !important;
            padding: 0 !important;
        }

        /* Ensure the report title is visible */
        .card-title {
            margin-bottom: 0.5rem !important;
            font-size: 14pt !important;
            font-weight: bold !important;
        }

        /* Add a footer with page numbers */
        @page {
            margin-bottom: 2cm;
        }

        html::after {
            content: "Page " counter(page) " of " counter(pages);
            position: fixed;
            bottom: 1cm;
            right: 1cm;
            font-size: 10pt;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    function toggleFilters() {
        var attendanceType = document.getElementById('type').value;
        var classFilterDiv = document.getElementById('classFilterDiv');
        var userLabel = document.getElementById('userLabel');
        var userSelect = document.getElementById('user_id');
        var isTeacher = {{ auth()->user()->role == 2 ? 'true' : 'false' }};
        
        if (attendanceType === 'student') {
            classFilterDiv.style.display = 'block';
            userLabel.textContent = 'Student';
            userSelect.options[0].text = 'All Students';
            userSelect.disabled = false;
        } else {
            classFilterDiv.style.display = 'none';
            if (isTeacher) {
                userLabel.textContent = 'My Record';
                userSelect.options[0].text = 'My Records';
                userSelect.disabled = true;
                // Force select the teacher's own record
                @if(auth()->user()->role == 2)
                userSelect.value = "{{ auth()->id() }}";
                @endif
            } else {
                userLabel.textContent = 'Teacher';
                userSelect.options[0].text = 'All Teachers';
                userSelect.disabled = false;
            }
        }
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        toggleFilters();
    });
</script>
@endpush