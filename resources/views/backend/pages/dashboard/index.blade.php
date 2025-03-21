@extends('backend.layouts.app')

@section('title', 'School Management Dashboard')

@section('styles')
<style>
    .traffic-summary,
    .sales-summary {
        padding: 1rem;
    }

    .traffic-source {
        padding: 1rem;
        border-radius: 5px;
        background: rgba(255, 255, 255, 0.1);
        margin-bottom: 1rem;
    }

    .progress-data {
        background: rgba(255, 255, 255, 0.1);
        padding: 1rem;
        border-radius: 5px;
    }

    .quick-link {
        display: block;
        padding: 15px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 5px;
        margin-bottom: 15px;
        transition: all 0.3s ease;
    }

    .quick-link:hover {
        background: rgba(255, 255, 255, 0.2);
        transform: translateY(-3px);
    }

    .quick-link i {
        font-size: 24px;
        margin-bottom: 10px;
    }

    .quick-stats {
        padding: 20px;
        border-radius: 5px;
        background: rgba(255, 255, 255, 0.1);
        margin-bottom: 20px;
    }

    .stat-icon {
        font-size: 36px;
        margin-bottom: 10px;
    }

    .stat-value {
        font-size: 24px;
        font-weight: bold;
    }

    .stat-label {
        font-size: 14px;
        opacity: 0.8;
    }

    .recent-activity {
        background: rgba(255, 255, 255, 0.1);
        border-radius: 5px;
        padding: 15px;
    }

    .activity-item {
        padding: 10px 0;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .activity-item:last-child {
        border-bottom: none;
    }

    .activity-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
    }

    .activity-time {
        font-size: 12px;
        opacity: 0.7;
    }
</style>
@endsection

@section('content')
<!--Start Dashboard Content-->
@if(Auth::user()->role == 1)
<div class="card mt-3">
    <div class="card-content">
        <div class="row row-group m-0">
            <div class="col-12 col-lg-6 col-xl-3 border-light">
                <div class="card-body">
                    <h5 class="text-white mb-0">{{ $totalStudents }} <span class="float-right"><i
                                class="zmdi zmdi-accounts"></i></span></h5>
                    <div class="progress my-3" style="height:3px;">
                        <div class="progress-bar" style="width:85%"></div>
                    </div>
                    <p class="mb-0 text-white small-font">Total Students <span class="float-right">+4.2% <i
                                class="zmdi zmdi-long-arrow-up"></i></span></p>
                </div>
            </div>
            <div class="col-12 col-lg-6 col-xl-3 border-light">
                <div class="card-body">
                    <h5 class="text-white mb-0">{{ $totalTeachers }} <span class="float-right"><i
                                class="zmdi zmdi-accounts-list"></i></span></h5>
                    <div class="progress my-3" style="height:3px;">
                        <div class="progress-bar" style="width:55%"></div>
                    </div>
                    <p class="mb-0 text-white small-font">Total Teachers <span class="float-right">+1.2% <i
                                class="zmdi zmdi-long-arrow-up"></i></span></p>
                </div>
            </div>
            <div class="col-12 col-lg-6 col-xl-3 border-light">
                <div class="card-body">
                    <h5 class="text-white mb-0">{{ $totalClasses }} <span class="float-right"><i
                                class="zmdi zmdi-graduation-cap"></i></span></h5>
                    <div class="progress my-3" style="height:3px;">
                        <div class="progress-bar" style="width:75%"></div>
                    </div>
                    <p class="mb-0 text-white small-font">Total Classes <span class="float-right">+5.2% <i
                                class="zmdi zmdi-long-arrow-up"></i></span></p>
                </div>
            </div>
            <div class="col-12 col-lg-6 col-xl-3 border-light">
                <div class="card-body">
                    <h5 class="text-white mb-0">PKR {{ number_format($totalFees) }} <span class="float-right"><i
                                class="zmdi zmdi-money"></i></span></h5>
                    <div class="progress my-3" style="height:3px;">
                        <div class="progress-bar" style="width:55%"></div>
                    </div>
                    <p class="mb-0 text-white small-font">Total Fees Collected <span class="float-right">+2.2% <i
                                class="zmdi zmdi-long-arrow-up"></i></span></p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12 col-lg-8 col-xl-8">
        <div class="card">
            <div class="card-header">Attendance Overview</div>
            <div class="card-body">
                <!-- Student Attendance -->
                <div class="traffic-summary mb-4">
                    <div class="row mb-3">
                        <div class="col-12">
                            <h4 class="mb-0">Student Attendance</h4>
                            <p class="text-muted">Last 30 days statistics</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-md-3">
                            <div class="traffic-source">
                                <h5><i class="fa fa-check-circle text-success mr-2"></i>Present</h5>
                                <p class="mb-0">{{ $attendanceData['student_present'] ?? $attendanceData['present'] }}%
                                    <span class="text-success"><i class="fa fa-arrow-up"></i> 3%</span>
                                </p>
                            </div>
                        </div>
                        <div class="col-12 col-md-3">
                            <div class="traffic-source">
                                <h5><i class="fa fa-times-circle text-danger mr-2"></i>Absent</h5>
                                <p class="mb-0">{{ $attendanceData['student_absent'] ?? $attendanceData['absent'] }}%
                                    <span class="text-success"><i class="fa fa-arrow-down"></i> 1%</span>
                                </p>
                            </div>
                        </div>
                        <div class="col-12 col-md-3">
                            <div class="traffic-source">
                                <h5><i class="fa fa-clock text-warning mr-2"></i>Late</h5>
                                <p class="mb-0">{{ $attendanceData['student_late'] ?? ($attendanceData['late'] ?? 0) }}%
                                    <span class="text-danger"><i class="fa fa-arrow-up"></i> 0.5%</span>
                                </p>
                            </div>
                        </div>
                        <div class="col-12 col-md-3">
                            <div class="traffic-source">
                                <h5><i class="fa fa-calendar-minus text-info mr-2"></i>Leave</h5>
                                <p class="mb-0">{{ $attendanceData['student_leave'] ?? $attendanceData['leave'] }}%
                                    <span class="text-danger"><i class="fa fa-arrow-up"></i> 0.5%</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Teacher Attendance -->
                <div class="traffic-summary">
                    <div class="row mb-3">
                        <div class="col-12">
                            <h4 class="mb-0">Teacher Attendance</h4>
                            <p class="text-muted">Last 30 days statistics</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-md-3">
                            <div class="traffic-source">
                                <h5><i class="fa fa-check-circle text-success mr-2"></i>Present</h5>
                                <p class="mb-0">{{ $attendanceData['teacher_present'] ?? ($attendanceData['teacher'] ??
                                    95) }}%
                                    <span class="text-success"><i class="fa fa-arrow-up"></i> 1.5%</span>
                                </p>
                            </div>
                        </div>
                        <div class="col-12 col-md-3">
                            <div class="traffic-source">
                                <h5><i class="fa fa-times-circle text-danger mr-2"></i>Absent</h5>
                                <p class="mb-0">{{ $attendanceData['teacher_absent'] ?? 2 }}%
                                    <span class="text-success"><i class="fa fa-arrow-down"></i> 0.5%</span>
                                </p>
                            </div>
                        </div>
                        <div class="col-12 col-md-3">
                            <div class="traffic-source">
                                <h5><i class="fa fa-clock text-warning mr-2"></i>Late</h5>
                                <p class="mb-0">{{ $attendanceData['teacher_late'] ?? 1 }}%
                                    <span class="text-success"><i class="fa fa-arrow-down"></i> 0.2%</span>
                                </p>
                            </div>
                        </div>
                        <div class="col-12 col-md-3">
                            <div class="traffic-source">
                                <h5><i class="fa fa-calendar-minus text-info mr-2"></i>Leave</h5>
                                <p class="mb-0">{{ $attendanceData['teacher_leave'] ?? 2 }}%
                                    <span class="text-success"><i class="fa fa-arrow-down"></i> 0.3%</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row m-0 row-group text-center border-top border-light-3">
                <div class="col-12 col-lg-4">
                    <div class="p-3">
                        <h5 class="mb-0">{{ $attendanceData['overall'] }}%</h5>
                        <small class="mb-0">Overall Attendance <span> <i class="fa fa-arrow-up"></i> 2.5%</span></small>
                    </div>
                </div>
                <div class="col-12 col-lg-4">
                    <div class="p-3">
                        <h5 class="mb-0">{{ $attendanceData['teacher'] }}%</h5>
                        <small class="mb-0">Teacher Attendance <span> <i class="fa fa-arrow-up"></i> 1.2%</span></small>
                    </div>
                </div>
                <div class="col-12 col-lg-4">
                    <div class="p-3">
                        <h5 class="mb-0">{{ $attendanceData['student'] }}%</h5>
                        <small class="mb-0">Student Attendance <span> <i class="fa fa-arrow-up"></i> 3.1%</span></small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="card mt-3">
            <div class="card-header">Recent Activities</div>
            <div class="card-body">
                <div class="recent-activity">
                    @foreach($recentActivities as $activity)
                    <div class="activity-item d-flex align-items-center">
                        <div class="activity-icon">
                            <i class="zmdi {{ $activity['icon'] }} text-white"></i>
                        </div>
                        <div class="activity-content flex-grow-1">
                            <h6 class="mb-1">{{ $activity['title'] }}</h6>
                            <p class="mb-0">{{ $activity['description'] }}</p>
                            @if(isset($activity['user']))
                            <small class="text-muted">By: {{ $activity['user']->name ?? 'System' }}</small>
                            @endif
                        </div>
                        <div class="activity-time">
                            <span>{{ is_string($activity['time']) ?
                                \Carbon\Carbon::parse($activity['time'])->diffForHumans() :
                                $activity['time']->diffForHumans() }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-4 col-xl-4">
        <div class="card">
            <div class="card-header">Fee Collection Summary</div>
            <div class="card-body">
                <div class="sales-summary">
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="sales-data">
                                <h5 class="mb-1">Total Fees</h5>
                                <h3 class="mb-1">PKR {{ number_format($totalFees) }}</h3>
                                <p class="mb-0 text-success"><i class="fa fa-arrow-up"></i> 12% This Month</p>
                            </div>
                        </div>
                    </div>
                    <div class="sales-categories">
                        <div class="progress-data mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <h6 class="mb-0">Tuition Fees</h6>
                                <h6 class="mb-0">PKR {{ number_format($tuitionFees) }}</h6>
                            </div>
                            <div class="progress" style="height: 5px;">
                                <div class="progress-bar" role="progressbar"
                                    style="width: {{ $totalFees > 0 ? ($tuitionFees / $totalFees * 100) : 0 }}%"></div>
                            </div>
                        </div>
                        <div class="progress-data mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <h6 class="mb-0">Exam Fees</h6>
                                <h6 class="mb-0">PKR {{ number_format($examFees) }}</h6>
                            </div>
                            <div class="progress" style="height: 5px;">
                                <div class="progress-bar" role="progressbar"
                                    style="width: {{ $totalFees > 0 ? ($examFees / $totalFees * 100) : 0 }}%"></div>
                            </div>
                        </div>
                        <div class="progress-data mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <h6 class="mb-0">Transport Fees</h6>
                                <h6 class="mb-0">PKR {{ number_format($transportFees) }}</h6>
                            </div>
                            <div class="progress" style="height: 5px;">
                                <div class="progress-bar" role="progressbar"
                                    style="width: {{ $totalFees > 0 ? ($transportFees / $totalFees * 100) : 0 }}%">
                                </div>
                            </div>
                        </div>
                        <div class="progress-data">
                            <div class="d-flex justify-content-between mb-1">
                                <h6 class="mb-0">Other Fees</h6>
                                <h6 class="mb-0">PKR {{ number_format($otherFees) }}</h6>
                            </div>
                            <div class="progress" style="height: 5px;">
                                <div class="progress-bar" role="progressbar"
                                    style="width: {{ $totalFees > 0 ? ($otherFees / $totalFees * 100) : 0 }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Links -->
        <div class="card mt-3">
            <div class="card-header">Quick Links</div>
            <div class="card-body">
                <div class="row">
                    <div class="col-6">
                        <a href="{{ url('/dash/fees') }}" class="quick-link text-center">
                            <i class="zmdi zmdi-money d-block"></i>
                            <span>Fees Management</span>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ url('/dash/students') }}" class="quick-link text-center">
                            <i class="zmdi zmdi-accounts d-block"></i>
                            <span>Students</span>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ url('/dash/teachers') }}" class="quick-link text-center">
                            <i class="zmdi zmdi-accounts-list d-block"></i>
                            <span>Teachers</span>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ url('/dash/timetable') }}" class="quick-link text-center">
                            <i class="zmdi zmdi-calendar d-block"></i>
                            <span>Timetable</span>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('attendance.students.index') }}" class="quick-link text-center">
                            <i class="zmdi zmdi-calendar-check d-block"></i>
                            <span>Attendance</span>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ url('/dash/messages') }}" class="quick-link text-center">
                            <i class="zmdi zmdi-email d-block"></i>
                            <span>Messages</span>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ url('/dash/public-report') }}" class="quick-link text-center">
                            <i class="zmdi zmdi-file-text d-block"></i>
                            <span>Reports</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12 col-lg-12">
        <div class="card">
            <div class="card-header">Recent Fee Payments</div>
            <div class="table-responsive">
                <table class="table align-items-center table-flush table-borderless">
                    <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>Student Name</th>
                            <th>Fee Type</th>
                            <th>Amount</th>
                            <th>Payment Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentPayments as $payment)
                        <tr>
                            <td>STD-{{ $payment->student_id }}</td>
                            <td>{{ $payment->student->name ?? 'Unknown Student' }}</td>
                            <td>{{ ucfirst($payment->fee_type) }}</td>
                            <td>PKR {{ number_format($payment->amount) }}</td>
                            <td>{{ $payment->payment_date ? $payment->payment_date->format('d M Y') : 'Not Paid' }}</td>
                            <td>
                                @if($payment->status == 'paid')
                                <span class="badge badge-success">Paid</span>
                                @elseif($payment->status == 'pending')
                                <span class="badge badge-warning">Pending</span>
                                @else
                                <span class="badge badge-danger">Overdue</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">No recent payments found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@else
<!-- Teacher Dashboard -->
<div class="card mt-3">
    <div class="card-content">
        <div class="row row-group m-0">
            <div class="col-12 col-lg-6 col-xl-4 border-light">
                <div class="card-body">
                    <h5 class="text-white mb-0">{{ $assignedClasses }} <span class="float-right"><i
                                class="zmdi zmdi-city"></i></span></h5>
                    <div class="progress my-3" style="height:3px;">
                        <div class="progress-bar" style="width:75%"></div>
                    </div>
                    <p class="mb-0 text-white small-font">Assigned Classes</p>
                </div>
            </div>
            <div class="col-12 col-lg-6 col-xl-4 border-light">
                <div class="card-body">
                    <h5 class="text-white mb-0">{{ $totalStudentsAssigned }} <span class="float-right"><i
                                class="zmdi zmdi-accounts"></i></span></h5>
                    <div class="progress my-3" style="height:3px;">
                        <div class="progress-bar" style="width:85%"></div>
                    </div>
                    <p class="mb-0 text-white small-font">Total Students</p>
                </div>
            </div>
            <div class="col-12 col-lg-6 col-xl-4 border-light">
                <div class="card-body">
                    <h5 class="text-white mb-0">{{ $averageStudentsPerClass }} <span class="float-right"><i
                                class="zmdi zmdi-chart"></i></span></h5>
                    <div class="progress my-3" style="height:3px;">
                        <div class="progress-bar" style="width:65%"></div>
                    </div>
                    <p class="mb-0 text-white small-font">Avg. Students/Class</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12 col-lg-8">
        <!-- Teacher's Timetable -->
        <div class="card mt-3">
            <div class="card-header">My Timetable</div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table align-items-center table-flush table-borderless">
                        <thead>
                            <tr>
                                <th>Time</th>
                                <th>Monday</th>
                                <th>Tuesday</th>
                                <th>Wednesday</th>
                                <th>Thursday</th>
                                <th>Friday</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($teacherTimetable as $time => $slots)
                            <tr>
                                <td>{{ $time }}</td>
                                <td>{{ $slots['monday'] }}</td>
                                <td>{{ $slots['tuesday'] }}</td>
                                <td>{{ $slots['wednesday'] }}</td>
                                <td>{{ $slots['thursday'] }}</td>
                                <td>{{ $slots['friday'] }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Class Overview -->
        <div class="card mt-3">
            <div class="card-header">Class Overview</div>
            <div class="card-body">
                <div class="traffic-summary">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="traffic-source">
                                <h5><i class="zmdi zmdi-accounts text-info mr-2"></i>Total Students</h5>
                                <p class="mb-0">{{ $totalStudentsAssigned }} Students</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="traffic-source">
                                <h5><i class="zmdi zmdi-graduation-cap text-success mr-2"></i>Average Attendance</h5>
                                <p class="mb-0">{{ $attendanceData['student'] ?? '95' }}%</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="traffic-source">
                                <h5><i class="zmdi zmdi-calendar text-warning mr-2"></i>Classes Today</h5>
                                <p class="mb-0">{{ count($teacherTimetable ?? []) }} Classes</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-4">
        <!-- Quick Links -->
        <div class="card mt-3">
            <div class="card-header">Quick Links</div>
            <div class="card-body">
                <div class="row">
                    <div class="col-6">
                        <a href="{{ url('/dash/attendance/students') }}" class="quick-link text-center">
                            <i class="zmdi zmdi-calendar-check d-block"></i>
                            <span>Attendance</span>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ url('/dash/messages') }}" class="quick-link text-center">
                            <i class="zmdi zmdi-email d-block"></i>
                            <span>Messages</span>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ url('/dash/notes') }}" class="quick-link text-center">
                            <i class="zmdi zmdi-file-text d-block"></i>
                            <span>Notes</span>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ url('/dash/fines') }}" class="quick-link text-center">
                            <i class="zmdi zmdi-money-off d-block"></i>
                            <span>Fines</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Today's Schedule -->
        <div class="card mt-3">
            <div class="card-header">Today's Schedule</div>
            <div class="card-body">
                <div class="traffic-summary">
                    @php
                    $today = strtolower(date('l'));
                    $todayClasses = collect($teacherTimetable)->map(function($slots) use ($today) {
                    return $slots[$today] ?? '-';
                    })->filter(function($class) {
                    return $class != '-';
                    });
                    @endphp
                    @if($todayClasses->count() > 0)
                    @foreach($todayClasses as $time => $class)
                    <div class="traffic-source">
                        <h5><i class="zmdi zmdi-time text-info mr-2"></i>{{ $time }}</h5>
                        <p class="mb-0">{{ $class }}</p>
                    </div>
                    @endforeach
                    @else
                    <div class="text-center">
                        <p class="mb-0">No classes scheduled for today</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endif
<!--End Dashboard Content-->
@endsection

@section('scripts')
<!-- Chart JS -->
<script src="{{ asset('assets/plugins/Chart.js/Chart.min.js') }}"></script>
<!-- Sparkline JS -->
<script src="{{ asset('assets/plugins/sparkline-charts/jquery.sparkline.min.js') }}"></script>
<script>
    $(document).ready(function() {
        // Initialize any dashboard-specific scripts here
    });
</script>
@endsection