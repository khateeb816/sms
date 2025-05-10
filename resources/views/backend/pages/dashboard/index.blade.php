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

        /* Timetable styles */
        .timetable-cell {
            min-height: 60px;
            vertical-align: middle;
        }

        .timetable-subject {
            font-weight: bold;
            margin-bottom: 5px;
        }

        .timetable-teacher {
            font-size: 12px;
            color: #6c757d;
        }

        .table-bordered td,
        .table-bordered th {
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .nav-tabs .nav-link {
            color: #ffffff;
        }

        .nav-tabs .nav-link.active {
            background-color: rgba(255, 255, 255, 0.2);
            border-color: rgba(255, 255, 255, 0.3);
            color: #ffffff;
            font-weight: bold;
        }

        .nav-tabs {
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }

        .children-timetable-tab {
            margin-bottom: 1rem;
            padding: 10px 15px;
            border-radius: 5px 5px 0 0;
            transition: all 0.3s ease;
        }

        .children-timetable-tab:hover {
            background-color: rgba(255, 255, 255, 0.15);
        }

        .card-header.bg-light {
            background-color: rgba(255, 255, 255, 0.1) !important;
            color: #ffffff;
        }

        .table-bordered {
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        tr.bg-light th {
            background-color: rgba(255, 255, 255, 0.1) !important;
            color: #ffffff;
        }
    </style>
@endsection

@section('content')
    <!--Start Dashboard Content-->
    @if (Auth::user()->role == 1)
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

        <!-- Attendance Overview -->
        <div class="card mt-3">
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
                                <p class="mb-0">{{ $studentPresent }}%
                                    <span class="text-success"><i class="fa fa-arrow-up"></i>
                                        {{ $studentPresent > 0 ? 'Active' : 'No Data' }}</span>
                                </p>
                            </div>
                        </div>
                        <div class="col-12 col-md-3">
                            <div class="traffic-source">
                                <h5><i class="fa fa-times-circle text-danger mr-2"></i>Absent</h5>
                                <p class="mb-0">{{ $studentAbsent }}%
                                    <span class="text-danger"><i class="fa fa-arrow-down"></i>
                                        {{ $studentAbsent > 0 ? 'Active' : 'No Data' }}</span>
                                </p>
                            </div>
                        </div>
                        <div class="col-12 col-md-3">
                            <div class="traffic-source">
                                <h5><i class="fa fa-clock text-warning mr-2"></i>Late</h5>
                                <p class="mb-0">{{ $studentLate }}%
                                    <span class="text-warning"><i class="fa fa-clock"></i>
                                        {{ $studentLate > 0 ? 'Active' : 'No Data' }}</span>
                                </p>
                            </div>
                        </div>
                        <div class="col-12 col-md-3">
                            <div class="traffic-source">
                                <h5><i class="fa fa-calendar-minus text-info mr-2"></i>Leave</h5>
                                <p class="mb-0">{{ $studentLeave }}%
                                    <span class="text-info"><i class="fa fa-calendar"></i>
                                        {{ $studentLeave > 0 ? 'Active' : 'No Data' }}</span>
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
                                <p class="mb-0">{{ $teacherPresent }}%
                                    <span class="text-success"><i class="fa fa-arrow-up"></i>
                                        {{ $teacherPresent > 0 ? 'Active' : 'No Data' }}</span>
                                </p>
                            </div>
                        </div>
                        <div class="col-12 col-md-3">
                            <div class="traffic-source">
                                <h5><i class="fa fa-times-circle text-danger mr-2"></i>Absent</h5>
                                <p class="mb-0">{{ $teacherAbsent }}%
                                    <span class="text-danger"><i class="fa fa-arrow-down"></i>
                                        {{ $teacherAbsent > 0 ? 'Active' : 'No Data' }}</span>
                                </p>
                            </div>
                        </div>
                        <div class="col-12 col-md-3">
                            <div class="traffic-source">
                                <h5><i class="fa fa-clock text-warning mr-2"></i>Late</h5>
                                <p class="mb-0">{{ $teacherLate }}%
                                    <span class="text-warning"><i class="fa fa-clock"></i>
                                        {{ $teacherLate > 0 ? 'Active' : 'No Data' }}</span>
                                </p>
                            </div>
                        </div>
                        <div class="col-12 col-md-3">
                            <div class="traffic-source">
                                <h5><i class="fa fa-calendar-minus text-info mr-2"></i>Leave</h5>
                                <p class="mb-0">{{ $teacherLeave }}%
                                    <span class="text-info"><i class="fa fa-calendar"></i>
                                        {{ $teacherLeave > 0 ? 'Active' : 'No Data' }}</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12 col-lg-8 col-xl-8">
                @if (Auth::user()->role == 2)
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
                                        @foreach ($teacherTimetable as $time => $slots)
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
                                    <div class="col-md-6">
                                        <div class="traffic-source">
                                            <h5><i class="zmdi zmdi-accounts text-info mr-2"></i>Total Students</h5>
                                            <p class="mb-0">{{ $totalStudentsAssigned }} Students</p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="traffic-source">
                                            <h5><i class="zmdi zmdi-calendar text-warning mr-2"></i>Classes Today</h5>
                                            <p class="mb-0">{{ $todayClasses }} Classes</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Recent Activity -->
                <div class="card mt-3">
                    <div class="card-header">Recent Activities</div>
                    <div class="card-body">
                        <div class="recent-activity">
                            @foreach ($recentActivities as $activity)
                                <div class="activity-item d-flex align-items-center">
                                    <div class="activity-icon">
                                        <i class="zmdi {{ $activity['icon'] }} text-white"></i>
                                    </div>
                                    <div class="activity-content flex-grow-1">
                                        <h6 class="mb-1">{{ $activity['title'] }}</h6>
                                        <p class="mb-0">{{ $activity['description'] }}</p>
                                        @if (isset($activity['user']))
                                            <small class="text-muted">By:
                                                {{ $activity['user']->name ?? 'System' }}</small>
                                        @endif
                                    </div>
                                    <div class="activity-time">
                                        <span>{{ is_string($activity['time'])
                                            ? \Carbon\Carbon::parse($activity['time'])->diffForHumans()
                                            : ($activity['time']
                                                ? $activity['time']->diffForHumans()
                                                : 'N/A') }}</span>
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
                                            style="width: {{ $totalFees > 0 ? ($tuitionFees / $totalFees) * 100 : 0 }}%">
                                        </div>
                                    </div>
                                </div>
                                <div class="progress-data mb-3">
                                    <div class="d-flex justify-content-between mb-1">
                                        <h6 class="mb-0">Exam Fees</h6>
                                        <h6 class="mb-0">PKR {{ number_format($examFees) }}</h6>
                                    </div>
                                    <div class="progress" style="height: 5px;">
                                        <div class="progress-bar" role="progressbar"
                                            style="width: {{ $totalFees > 0 ? ($examFees / $totalFees) * 100 : 0 }}%">
                                        </div>
                                    </div>
                                </div>
                                <div class="progress-data mb-3">
                                    <div class="d-flex justify-content-between mb-1">
                                        <h6 class="mb-0">Transport Fees</h6>
                                        <h6 class="mb-0">PKR {{ number_format($transportFees) }}</h6>
                                    </div>
                                    <div class="progress" style="height: 5px;">
                                        <div class="progress-bar" role="progressbar"
                                            style="width: {{ $totalFees > 0 ? ($transportFees / $totalFees) * 100 : 0 }}%">
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
                                            style="width: {{ $totalFees > 0 ? ($otherFees / $totalFees) * 100 : 0 }}%">
                                        </div>
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
                                    <span>Fees</span>
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
                                        <td>{{ $payment->payment_date ? $payment->payment_date->format('d M Y') : 'Not Paid' }}
                                        </td>
                                        <td>
                                            @if ($payment->status == 'paid')
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
    @elseif(Auth::user()->role == 2)
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
                                    @foreach ($teacherTimetable as $time => $slots)
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
                                <div class="col-md-6">
                                    <div class="traffic-source">
                                        <h5><i class="zmdi zmdi-accounts text-info mr-2"></i>Total Students</h5>
                                        <p class="mb-0">{{ $totalStudentsAssigned }} Students</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="traffic-source">
                                        <h5><i class="zmdi zmdi-calendar text-warning mr-2"></i>Classes Today</h5>
                                        <p class="mb-0">{{ $todayClasses }} Classes</p>
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
                                $todayClasses = collect($teacherTimetable)
                                    ->map(function ($slots) use ($today) {
                                        return $slots[$today] ?? '-';
                                    })
                                    ->filter(function ($class) {
                                        return $class != '-';
                                    });
                            @endphp
                            @if ($todayClasses->count() > 0)
                                @foreach ($todayClasses as $time => $class)
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
    @elseif(Auth::user()->role == 3)
        <!-- Parent Dashboard -->
        <div class="card mt-3">
            <div class="card-content">
                <div class="row row-group m-0">
                    <div class="col-12 col-lg-6 col-xl-4 border-light">
                        <div class="card-body">
                            <h5 class="text-white mb-0">{{ $totalChildren }} <span class="float-right"><i
                                        class="zmdi zmdi-accounts"></i></span></h5>
                            <div class="progress my-3" style="height:3px;">
                                <div class="progress-bar" style="width:75%"></div>
                            </div>
                            <p class="mb-0 text-white small-font">Total Children</p>
                        </div>
                    </div>
                    <div class="col-12 col-lg-6 col-xl-4 border-light">
                        <div class="card-body">
                            <h5 class="text-white mb-0">{{ $totalUnpaidFees }} <span class="float-right"><i
                                        class="zmdi zmdi-money-off"></i></span></h5>
                            <div class="progress my-3" style="height:3px;">
                                <div class="progress-bar" style="width:85%"></div>
                            </div>
                            <p class="mb-0 text-white small-font">Unpaid Fees</p>
                        </div>
                    </div>
                    <div class="col-12 col-lg-6 col-xl-4 border-light">
                        <div class="card-body">
                            <h5 class="text-white mb-0">{{ $totalUnpaidFines }} <span class="float-right"><i
                                        class="zmdi zmdi-warning"></i></span></h5>
                            <div class="progress my-3" style="height:3px;">
                                <div class="progress-bar" style="width:65%"></div>
                            </div>
                            <p class="mb-0 text-white small-font">Unpaid Fines</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12 col-lg-8">
                <!-- Children's Attendance Overview -->
                <div class="card mt-3">
                    <div class="card-header">Children's Attendance Overview</div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table align-items-center table-flush table-borderless">
                                <thead>
                                    <tr>
                                        <th>Child Name</th>
                                        <th>Present</th>
                                        <th>Absent</th>
                                        <th>Late</th>
                                        <th>Leave</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($children as $child)
                                        <tr>
                                            <td>{{ $child->name }}</td>
                                            <td>{{ $attendanceOverview[$child->id]['present'] ?? 0 }}</td>
                                            <td>{{ $attendanceOverview[$child->id]['absent'] ?? 0 }}</td>
                                            <td>{{ $attendanceOverview[$child->id]['late'] ?? 0 }}</td>
                                            <td>{{ $attendanceOverview[$child->id]['leave'] ?? 0 }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Upcoming Exams -->
                <div class="card mt-3">
                    <div class="card-header">Upcoming Exams</div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table align-items-center table-flush table-borderless">
                                <thead>
                                    <tr>
                                        <th>Exam Name</th>
                                        <th>Date</th>
                                        <th>Class</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($upcomingExams as $exam)
                                        <tr>
                                            <td>{{ $exam->subject }}</td>
                                            <td>{{ \Carbon\Carbon::parse($exam->exam_date)->format('d M Y') }}</td>
                                            <td>{{ $exam->class->name ?? 'N/A' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center">No upcoming exams</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
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
                                <a href="{{ url('/dash/parent-fees') }}" class="quick-link text-center">
                                    <i class="zmdi zmdi-money d-block"></i>
                                    <span>Fees</span>
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="{{ url('/dash/attendance/parent') }}" class="quick-link text-center">
                                    <i class="zmdi zmdi-calendar-check d-block"></i>
                                    <span>Attendance</span>
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="{{ url('/dash/exam/results') }}" class="quick-link text-center">
                                    <i class="zmdi zmdi-graduation-cap d-block"></i>
                                    <span>Results</span>
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="{{ url('/dash/messages') }}" class="quick-link text-center">
                                    <i class="zmdi zmdi-email d-block"></i>
                                    <span>Messages</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Results -->
                <div class="card mt-3">
                    <div class="card-header">Recent Results</div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table align-items-center table-flush table-borderless">
                                <thead>
                                    <tr>
                                        <th>Child</th>
                                        <th>Subject</th>
                                        <th>Grade</th>
                                        <th>Score</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentResults as $result)
                                        <tr>
                                            <td>{{ $result->student->name ?? 'Unknown' }}</td>
                                            <td>{{ $result->exam->subject ?? 'N/A' }}</td>
                                            <td>
                                                <span
                                                    class="badge {{ $result->is_passed ? 'badge-success' : 'badge-danger' }}">
                                                    {{ $result->grade ?? '-' }}
                                                </span>
                                            </td>
                                            <td>{{ $result->percentage ?? 0 }}%</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center">No recent results</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                
            </div>

            <div class="col-12">
                <!-- Unpaid Fees -->
                <div class="card mt-3">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Unpaid Fees</h5>
                        <a href="{{ route('fees.parent') }}" class="btn btn-primary btn-sm">View All Fees & Fines</a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table align-items-center table-flush table-borderless">
                                <thead>
                                    <tr>
                                        <th>Child</th>
                                        <th>Type</th>
                                        <th>Amount</th>
                                        <th>Due Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($unpaidFees as $fee)
                                        <tr>
                                            <td>{{ $fee->student->name }}</td>
                                            <td>{{ ucfirst($fee->fee_type) }}</td>
                                            <td>PKR {{ number_format($fee->amount) }}</td>
                                            <td>{{ \Carbon\Carbon::parse($fee->due_date)->format('d M Y') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center">No unpaid fees</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Children's Timetables -->
                <div class="card mt-3">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Children's Timetables</h5>
                        <span class="badge badge-info p-2">Click on child's name to view their timetable</span>
                    </div>
                    <div class="card-body p-0">
                        <div class="p-3">
                            <ul class="nav nav-tabs" id="childrenTimetableTabs" role="tablist">
                                @foreach ($childrenTimetables as $childId => $childData)
                                    <li class="nav-item">
                                        <a class="nav-link children-timetable-tab {{ $loop->first ? 'active' : '' }}"
                                            id="child-{{ $childId }}-tab" data-toggle="tab"
                                            href="#child-{{ $childId }}" role="tab"
                                            aria-controls="child-{{ $childId }}"
                                            aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                                            {{ $childData['child_name'] }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>

                            <div class="tab-content mt-3" id="childrenTimetableContent">
                                @foreach ($childrenTimetables as $childId => $childData)
                                    <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}"
                                        id="child-{{ $childId }}" role="tabpanel"
                                        aria-labelledby="child-{{ $childId }}-tab">

                                        @if (count($childData['class_timetables']) > 0)
                                            @foreach ($childData['class_timetables'] as $classTimetable)
                                                <div class="card mb-3">
                                                    <div class="card-header bg-light">
                                                        <h5 class="mb-0">{{ $classTimetable['class_name'] }}</h5>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="table-responsive">
                                                            <table class="table table-bordered">
                                                                <thead>
                                                                    <tr class="bg-light">
                                                                        <th width="15%">Time</th>
                                                                        <th>Monday</th>
                                                                        <th>Tuesday</th>
                                                                        <th>Wednesday</th>
                                                                        <th>Thursday</th>
                                                                        <th>Friday</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @php
                                                                        // Get all unique time slots
                                                                        $timeSlots = [];
                                                                        foreach (
                                                                            [
                                                                                'monday',
                                                                                'tuesday',
                                                                                'wednesday',
                                                                                'thursday',
                                                                                'friday',
                                                                            ]
                                                                            as $day
                                                                        ) {
                                                                            foreach (
                                                                                $classTimetable['timetable'][$day]
                                                                                as $slot
                                                                            ) {
                                                                                $timeSlots[$slot['time']] = true;
                                                                            }
                                                                        }
                                                                        // Sort time slots
                                                                        ksort($timeSlots);
                                                                        $timeSlots = array_keys($timeSlots);
                                                                    @endphp

                                                                    @foreach ($timeSlots as $timeSlot)
                                                                        <tr>
                                                                            <td><strong>{{ $timeSlot }}</strong></td>
                                                                            @foreach (['monday', 'tuesday', 'wednesday', 'thursday', 'friday'] as $day)
                                                                                <td class="timetable-cell">
                                                                                    @php
                                                                                        $found = false;
                                                                                        foreach (
                                                                                            $classTimetable[
                                                                                                'timetable'
                                                                                            ][$day]
                                                                                            as $slot
                                                                                        ) {
                                                                                            if (
                                                                                                $slot['time'] ==
                                                                                                $timeSlot
                                                                                            ) {
                                                                                                $found = true;
                                                                                                echo "<div class='timetable-subject'>{$slot['subject']}</div>";
                                                                                                echo "<div class='timetable-teacher'>{$slot['teacher']}</div>";
                                                                                                break;
                                                                                            }
                                                                                        }
                                                                                        if (!$found) {
                                                                                            echo '-';
                                                                                        }
                                                                                    @endphp
                                                                                </td>
                                                                            @endforeach
                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @else
                                            <div class="alert alert-info">
                                                No timetable available for {{ $childData['child_name'] }}.
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    @endif
    <!--End Dashboard Content-->
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize the children's timetable tabs
            const tabLinks = document.querySelectorAll('#childrenTimetableTabs a');
            tabLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const tabId = this.getAttribute('href');
                    const tabContent = document.querySelector(tabId);

                    // Hide all tab content
                    document.querySelectorAll('.tab-pane').forEach(pane => {
                        pane.classList.remove('active', 'show');
                    });

                    // Remove active class from all tabs
                    tabLinks.forEach(tab => {
                        tab.classList.remove('active');
                    });

                    // Show selected tab content and mark tab as active
                    tabContent.classList.add('active', 'show');
                    this.classList.add('active');
                });
            });

            // Handle hash in URL for direct tab access
            if (window.location.hash) {
                const hash = window.location.hash;
                const targetTab = document.querySelector('#childrenTimetableTabs a[href="' + hash + '"]');
                if (targetTab) {
                    targetTab.click();
                }
            }

            // Remember the last active tab
            document.querySelectorAll('a[data-toggle="tab"]').forEach(tab => {
                tab.addEventListener('shown.bs.tab', function(e) {
                    localStorage.setItem('lastActiveChildTab', this.getAttribute('href'));
                });
            });

            // Get the last active tab if it exists
            const lastActiveChildTab = localStorage.getItem('lastActiveChildTab');
            if (lastActiveChildTab) {
                const lastTab = document.querySelector('#childrenTimetableTabs a[href="' + lastActiveChildTab +
                    '"]');
                if (lastTab) {
                    lastTab.click();
                }
            }
        });
    </script>
@endsection

@section('scripts')
    <!-- Chart JS -->
    <script src="{{ asset('assets/plugins/Chart.js/Chart.min.js') }}"></script>
    <!-- Sparkline JS -->
    <script src="{{ asset('assets/plugins/sparkline-charts/jquery.sparkline.min.js') }}"></script>
@endsection
