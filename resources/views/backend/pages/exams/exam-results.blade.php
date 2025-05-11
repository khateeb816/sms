@extends('backend.layouts.app')

@section('title', 'Exam Results')

@section('content')
<div class=" ">
    <div class="container-fluid">
        <!-- Breadcrumb-->
        <div class="row pt-2 pb-2">
            <div class="col-sm-6">
                <h4 class="page-title">Exam Results</h4>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Exam Results</li>
                </ol>
            </div>
            <div class="col-sm-6">
                <div class="btn-group float-sm-right">
                    @if(Auth::user()->role === 1)
                        <a href="{{ route('exams.results.index', array_merge(request()->all(), ['nocache' => 1])) }}" class="btn btn-outline-secondary waves-effect waves-light mr-2">
                            <i class="fa fa-refresh mr-1"></i> Refresh Data
                        </a>
                    @else
                        <a href="{{ route('exams.results.index', array_merge(request()->all(), ['nocache' => 1])) }}" class="btn btn-outline-secondary waves-effect waves-light mr-2">
                            <i class="fa fa-refresh mr-1"></i> Refresh Data
                        </a>
                    @endif
                    <button type="button" class="btn btn-outline-primary waves-effect waves-light mr-2" id="printAllResults">
                        <i class="fa fa-print mr-1"></i> Print All Results
                    </button>
                    <button type="button" class="btn btn-outline-info waves-effect waves-light" id="downloadAllPdf">
                        <i class="fa fa-download mr-1"></i> Download All PDF
                    </button>
                </div>
            </div>
        </div>

        <!-- Filters Section -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <i class="fa fa-filter mr-2"></i> Filter Results
            </div>
            <div class="card-body">
                <form action="{{ route('exams.results.index') }}" method="GET" class="filter-form">
                    <div class="row">
                        @if(Auth::user()->role !== 4) {{-- Not showing filters for students --}}
                            @if(Auth::user()->role === 1 || Auth::user()->role === 2) {{-- Admin and Teacher can filter by class --}}
                                <div class="col-md-3 mb-3">
                                    <label for="class_id">Class</label>
                                    <select name="class_id" id="class_id" class="form-control">
                                        <option value="">All Classes</option>
                                        @foreach($classes as $class)
                                            <option value="{{ $class->id }}" {{ $filters->class_id == $class->id ? 'selected' : '' }}>
                                                {{ $class->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif

                            <div class="col-md-3 mb-3">
                                <label for="student_id">Student</label>
                                <select name="student_id" id="student_id" class="form-control">
                                    <option value="">All Students</option>
                                    @foreach($students as $student)
                                        <option value="{{ $student->id }}" {{ $filters->student_id == $student->id ? 'selected' : '' }}>
                                            {{ $student->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <div class="col-md-3 mb-3">
                            <label for="term">Term</label>
                            <select name="term" id="term" class="form-control">
                                <option value="">All Terms</option>
                                @foreach($terms as $key => $term)
                                    <option value="{{ $key }}" {{ $filters->term == $key ? 'selected' : '' }}>
                                        {{ $term }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label for="date">Date</label>
                            <input type="date" name="date" id="date" class="form-control" value="{{ $filters->date }}">
                        </div>

                        <div class="col-md-3 mb-3">
                            <label for="subject">Subject</label>
                            <select name="subject" id="subject" class="form-control">
                                <option value="">All Subjects</option>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject }}" {{ $filters->subject == $subject ? 'selected' : '' }}>
                                        {{ $subject }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3 mb-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary mr-2">
                                <i class="fa fa-search mr-1"></i> Filter
                            </button>
                            <a href="{{ route('exams.results.index') }}" class="btn btn-outline-secondary">
                                <i class="fa fa-times mr-1"></i> Clear
                            </a>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label for="per_page">Results Per Page</label>
                            <select name="per_page" id="per_page" class="form-control">
                                <option value="5" {{ request()->input('per_page') == 5 ? 'selected' : '' }}>5</option>
                                <option value="10" {{ request()->input('per_page') == 10 || !request()->has('per_page') ? 'selected' : '' }}>10</option>
                                <option value="25" {{ request()->input('per_page') == 25 ? 'selected' : '' }}>25</option>
                                <option value="50" {{ request()->input('per_page') == 50 ? 'selected' : '' }}>50</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- End Filters Section -->

        @if($datesheets->count() > 0)
            @foreach($datesheets as $datesheet)
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white d-flex flex-column py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-0 font-weight-bold">{{ $datesheet->title }}</h5>
                                <div class="mt-2 text-white-50 small">
                                    <span class="mr-3"><i class="fa fa-chalkboard-teacher mr-1"></i>{{ $datesheet->class->name }}</span>
                                    <span><i class="fa fa-calendar-alt mr-1"></i>{{ ucfirst($datesheet->term) }} Term</span>
                                </div>
                            </div>
                            <div>
                                <button type="button" class="btn btn-light btn-sm print-datesheet-btn mr-2" data-datesheet-id="{{ $datesheet->id }}">
                                    <i class="fa fa-print mr-1"></i> Print Result
                                </button>
                                <button type="button" class="btn btn-light btn-sm download-pdf-btn" data-datesheet-id="{{ $datesheet->id }}">
                                    <i class="fa fa-download mr-1"></i> Download PDF
                                </button>
                            </div>
                        </div>
                        <div class="mt-3">
                            <span class="badge badge-light px-3 py-2">
                                <i class="fa fa-clock mr-1"></i>
                                {{ \Carbon\Carbon::parse($datesheet->start_date)->format('jS F Y h:i A') }} - {{ \Carbon\Carbon::parse($datesheet->end_date)->format('jS F Y h:i A') }}
                            </span>
                        </div>
                    </div>

                    <div class="card-body">
                        @if($datesheet->description || $datesheet->instructions)
                            <div class="row mb-4">
                                @if($datesheet->description)
                                    <div class="col-md-6">
                                        <p class="mb-0"><strong>Description:</strong> {{ $datesheet->description }}</p>
                                    </div>
                                @endif
                                @if($datesheet->instructions)
                                    <div class="col-md-6">
                                        <p class="mb-0"><strong>Instructions:</strong> {{ $datesheet->instructions }}</p>
                                    </div>
                                @endif
                            </div>
                        @endif

                        @foreach($groupedResults as $studentName => $datesheetResults)
                            @if(isset($datesheetResults[$datesheet->id]))
                                <div class="student-results mb-4">
                                    <h6 class="border-bottom pb-2">Results for {{ $studentName }}</h6>

                                    <div class="row">
                                        @foreach($datesheetResults[$datesheet->id]['subjects'] as $subjectData)
                                            <div class="col-md-6 col-lg-4 mb-3">
                                                <div class="card h-100">
                                                    <div class="card-header d-flex justify-content-between align-items-center">
                                                        <h6 class="mb-0">{{ $subjectData['subject'] }}</h6>
                                                        <span class="badge {{ $subjectData['result']->is_passed ? 'badge-success' : 'badge-danger' }}">
                                                            {{ $subjectData['result']->is_passed ? 'Passed' : 'Failed' }}
                                                        </span>
                                                    </div>
                                                    <div class="card-body">
                                                        <p class="mb-1">Marks: {{ $subjectData['result']->marks_obtained }}/{{ $subjectData['exam']->total_marks }}</p>
                                                        <p class="mb-1">Percentage: {{ number_format($subjectData['result']->percentage, 2) }}%</p>
                                                        <p class="mb-0">Grade: {{ $subjectData['result']->grade }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endforeach

            <!-- Pagination links -->
            @if($datesheets instanceof \Illuminate\Pagination\LengthAwarePaginator)
            <div class="row mt-3">
                <div class="col-12 d-flex justify-content-center">
                    {{ $datesheets->appends(request()->except('page'))->links() }}
                </div>
            </div>
            @endif
        @else
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="fa fa-search fa-3x   mb-3"></i>
                    <h4>No Exam Results Found</h4>
                    <p class="  mb-0">
                        @if(Auth::user()->role === 3)
                            There are no published exam results for your children at this time. Please check back later.
                        @elseif(Auth::user()->role === 2)
                            There are no published exam results for your classes at this time. Please check back later.
                        @else
                            There are no published exam results matching your filters. Try adjusting your search criteria.
                        @endif
                    </p>
                </div>
            </div>
        @endif
    </div>
</div>
<!-- Include jsPDF library with all modules fully loaded -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script>
    // Define routes for downloads
    var downloadRoutes = {
        @foreach($datesheets as $datesheet)
            '{{ $datesheet->id }}': '{{ route("exams.results.download", $datesheet->id) }}',
        @endforeach
    };

    document.addEventListener('DOMContentLoaded', function() {
        // Class filter change event
        var classSelect = document.getElementById('class_id');
        if (classSelect) {
            classSelect.addEventListener('change', function() {
                var classId = this.value;
                if (classId) {
                    var xhr = new XMLHttpRequest();
                    xhr.open('GET', '{{ route("class.students") }}?class_id=' + classId);
                    xhr.onload = function() {
                        if (xhr.status === 200) {
                            var data = JSON.parse(xhr.responseText);
                            var options = '<option value="">All Students</option>';
                            data.forEach(function(student) {
                                options += '<option value="' + student.id + '">' + student.name + '</option>';
                            });
                            document.getElementById('student_id').innerHTML = options;
                        }
                    };
                    xhr.send();
                }
            });
        }

        // Print all results
        var printAllBtn = document.getElementById('printAllResults');
        if (printAllBtn) {
            printAllBtn.addEventListener('click', function() {
                var printWindow = window.open('', '_blank');
                var content = '';

                @foreach($datesheets as $datesheet)
                    content += generateDatesheetContent(@json($datesheet), @json($groupedResults));
                @endforeach

                printWindow.document.write(
                    '<!DOCTYPE html>' +
                    '<html>' +
                    '<head>' +
                        '<title>All Exam Results</title>' +
                        '<style>' +
                            '@media print {' +
                                '.no-print { display: none; }' +
                                '.page-break { page-break-after: always; }' +
                            '}' +
                            'body { font-family: Arial, sans-serif; }' +
                            '.print-header { text-align: center; margin-bottom: 20px; }' +
                            '.school-name { font-size: 24px; font-weight: bold; }' +
                            '.report-type { font-size: 18px; color: #666; }' +
                            '.print-footer { text-align: center; margin-top: 20px; font-size: 12px; color: #666; }' +
                        '</style>' +
                    '</head>' +
                    '<body>' +
                        '<div class="print-header">' +
                            '<div class="school-name">School Management System</div>' +
                            '<div class="report-type">All Examination Results Report</div>' +
                        '</div>' +
                        '<button onclick="window.print()" class="no-print">Print Report</button>' +
                        content +
                        '<div class="print-footer">' +
                            '<p>This is a computer generated report and does not require signature.</p>' +
                            '<p>Printed on: ' + new Date().toLocaleString() + '</p>' +
                        '</div>' +
                    '</body>' +
                    '</html>'
                );

                printWindow.document.close();
            });
        }

        // Download all results as PDF
        var downloadAllPdfBtn = document.getElementById('downloadAllPdf');
        if (downloadAllPdfBtn) {
            downloadAllPdfBtn.addEventListener('click', function() {
                window.location.href = '{{ route("exams.results.download-all") }}' + window.location.search;
            });
        }

        // Print individual result
        var printButtons = document.getElementsByClassName('print-datesheet-btn');
        Array.from(printButtons).forEach(function(button) {
            button.addEventListener('click', function() {
                var datesheetId = this.getAttribute('data-datesheet-id');
                var printWindow = window.open('', '_blank');
                var content = '';

                @foreach($datesheets as $datesheet)
                    if ({{ $datesheet->id }} === parseInt(datesheetId)) {
                        content = generateDatesheetContent(@json($datesheet), @json($groupedResults));
                    }
                @endforeach

                printWindow.document.write(
                    '<!DOCTYPE html>' +
                    '<html>' +
                    '<head>' +
                        '<title>Exam Result</title>' +
                        '<style>' +
                            '@media print {' +
                                '.no-print { display: none; }' +
                                '.page-break { page-break-after: always; }' +
                            '}' +
                            'body { font-family: Arial, sans-serif; }' +
                            '.print-header { text-align: center; margin-bottom: 20px; }' +
                            '.school-name { font-size: 24px; font-weight: bold; }' +
                            '.report-type { font-size: 18px; color: #666; }' +
                            '.print-footer { text-align: center; margin-top: 20px; font-size: 12px; color: #666; }' +
                        '</style>' +
                    '</head>' +
                    '<body>' +
                        '<div class="print-header">' +
                            '<div class="school-name">School Management System</div>' +
                            '<div class="report-type">Examination Result</div>' +
                        '</div>' +
                        '<button onclick="window.print()" class="no-print">Print Report</button>' +
                        content +
                        '<div class="print-footer">' +
                            '<p>This is a computer generated report and does not require signature.</p>' +
                            '<p>Printed on: ' + new Date().toLocaleString() + '</p>' +
                        '</div>' +
                    '</body>' +
                    '</html>'
                );

                printWindow.document.close();
            });
        });

        // Download individual result as PDF
        var downloadButtons = document.getElementsByClassName('download-pdf-btn');
        Array.from(downloadButtons).forEach(function(button) {
            button.addEventListener('click', function() {
                var datesheetId = this.getAttribute('data-datesheet-id');
                window.location.href = downloadRoutes[datesheetId];
            });
        });
    });

    function generateDatesheetContent(datesheet, groupedResults) {
        var content =
            '<div class="datesheet-results page-break">' +
                '<h3 style="color: #2d63c8; margin-bottom: 10px;">' + datesheet.title + '</h3>' +
                '<div style="margin-bottom: 15px;">' +
                    '<span style="margin-right: 20px;"><strong>Class:</strong> ' + datesheet.class.name + '</span>' +
                    '<span><strong>Term:</strong> ' + datesheet.term.charAt(0).toUpperCase() + datesheet.term.slice(1) + '</span>' +
                    '<span style="margin-left: 20px;"><strong>Date:</strong> ' + datesheet.start_date + ' - ' + datesheet.end_date + '</span>' +
                '</div>';

        if (datesheet.description || datesheet.instructions) {
            content += '<div style="margin-bottom: 15px; padding: 10px; background-color: #f8f9fa; border-radius: 5px;">';
            if (datesheet.description) {
                content += '<p><strong>Description:</strong> ' + datesheet.description + '</p>';
            }
            if (datesheet.instructions) {
                content += '<p><strong>Instructions:</strong> ' + datesheet.instructions + '</p>';
            }
            content += '</div>';
        }

        // Loop through each student's results for this datesheet
        for (var studentName in groupedResults) {
            if (groupedResults[studentName][datesheet.id]) {
                content +=
                    '<div style="margin-top: 20px;">' +
                        '<h4 style="border-bottom: 1px solid #ddd; padding-bottom: 5px;">Results for ' + studentName + '</h4>' +
                        '<table style="width: 100%; border-collapse: collapse; margin-top: 10px;">' +
                            '<thead>' +
                                '<tr style="background-color: #f0f0f0;">' +
                                    '<th style="padding: 8px; text-align: left; border: 1px solid #ddd;">Subject</th>' +
                                    '<th style="padding: 8px; text-align: center; border: 1px solid #ddd;">Marks</th>' +
                                    '<th style="padding: 8px; text-align: center; border: 1px solid #ddd;">Percentage</th>' +
                                    '<th style="padding: 8px; text-align: center; border: 1px solid #ddd;">Grade</th>' +
                                    '<th style="padding: 8px; text-align: center; border: 1px solid #ddd;">Status</th>' +
                                '</tr>' +
                            '</thead>' +
                            '<tbody>';

                var totalMarks = 0;
                var totalObtained = 0;
                var subjectCount = 0;

                // Add each subject result
                groupedResults[studentName][datesheet.id].subjects.forEach(function(subjectData) {
                    var statusColor = subjectData.result.is_passed ? 'green' : 'red';
                    var statusText = subjectData.result.is_passed ? 'Passed' : 'Failed';

                    content +=
                        '<tr>' +
                            '<td style="padding: 8px; border: 1px solid #ddd;">' + subjectData.subject + '</td>' +
                            '<td style="padding: 8px; text-align: center; border: 1px solid #ddd;">' + subjectData.result.marks_obtained + '/' + subjectData.exam.total_marks + '</td>' +
                            '<td style="padding: 8px; text-align: center; border: 1px solid #ddd;">' + parseFloat(subjectData.result.percentage).toFixed(2) + '%</td>' +
                            '<td style="padding: 8px; text-align: center; border: 1px solid #ddd;">' + subjectData.result.grade + '</td>' +
                            '<td style="padding: 8px; text-align: center; border: 1px solid #ddd; color: ' + statusColor + ';">' + statusText + '</td>' +
                        '</tr>';

                    totalMarks += parseInt(subjectData.exam.total_marks);
                    totalObtained += parseInt(subjectData.result.marks_obtained);
                    subjectCount++;
                });

                // Calculate overall results
                var overallPercentage = totalMarks > 0 ? ((totalObtained / totalMarks) * 100).toFixed(2) : 0;
                var overallStatus = parseFloat(overallPercentage) >= 40 ? 'Passed' : 'Failed';
                var statusColor = overallStatus === 'Passed' ? 'green' : 'red';

                // Add summary row
                content +=
                            '<tr style="font-weight: bold; background-color: #f8f9fa;">' +
                                '<td style="padding: 8px; border: 1px solid #ddd;">Overall Result</td>' +
                                '<td style="padding: 8px; text-align: center; border: 1px solid #ddd;">' + totalObtained + '/' + totalMarks + '</td>' +
                                '<td style="padding: 8px; text-align: center; border: 1px solid #ddd;">' + overallPercentage + '%</td>' +
                                '<td style="padding: 8px; text-align: center; border: 1px solid #ddd;">-</td>' +
                                '<td style="padding: 8px; text-align: center; border: 1px solid #ddd; color: ' + statusColor + ';">' + overallStatus + '</td>' +
                            '</tr>' +
                        '</tbody>' +
                    '</table>' +
                '</div>';
            }
        }

        content += '</div>'; // Close datesheet-results div
        return content;
    }
</script>

@push('styles')
<style>
    @media print {
        .no-print { display: none !important; }
        .page-break { page-break-after: always; }
    }
    .print-header {
        text-align: center;
        margin-bottom: 20px;
    }
    .school-name {
        font-size: 24px;
        font-weight: bold;
    }
    .report-type {
        font-size: 18px;
        color: #666;
    }
    .print-footer {
        text-align: center;
        margin-top: 20px;
        font-size: 12px;
        color: #666;
    }
</style>
@endpush
@endsection

