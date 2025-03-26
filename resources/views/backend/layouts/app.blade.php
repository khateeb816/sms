<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>@yield('title', 'Dashtreme Admin - Laravel Dashboard')</title>
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Storage;
    @endphp

    <!-- Custom Alert Styling -->
    <style>
        /* Alert Styling */
        .alert-container {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 9999;
            width: 100%;
            max-width: 450px;
            padding: 0 20px;
        }

        .alert {
            margin: 0;
            padding: 16px 20px !important;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
            animation: slideDown 0.4s ease-out forwards;
            opacity: 0;
            transform: translateY(-100%);
            display: flex;
            align-items: center;
            font-size: 14px;
            line-height: 1.5;
            border: none;
            background: #fff;
        }

        .alert.hide {
            animation: slideUp 0.4s ease-out forwards;
        }

        .alert-success {
            color: #2e7d32;
            background: #e8f5e9;
            border-left: 4px solid #2e7d32;
        }

        .alert-success i {
            color: #2e7d32;
        }

        .alert-danger {
            color: #c62828;
            background: #ffebee;
            border-left: 4px solid #c62828;
        }

        .alert-danger i {
            color: #c62828;
        }

        .alert-warning {
            color: #ef6c00;
            background: #fff3e0;
            border-left: 4px solid #ef6c00;
        }

        .alert-warning i {
            color: #ef6c00;
        }

        .alert-info {
            color: #1565c0;
            background: #e3f2fd;
            border-left: 4px solid #1565c0;
        }

        .alert-info i {
            color: #1565c0;
        }

        .alert .close {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: inherit;
            opacity: 0.5;
            transition: all 0.2s ease;
            background: none;
            border: none;
            padding: 4px;
            font-size: 18px;
            line-height: 1;
            cursor: pointer;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            margin: 0;
        }

        .alert .close span {
            line-height: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            height: 100%;
        }

        .alert .close:hover {
            opacity: 1;
            background: rgba(0, 0, 0, 0.05);
        }

        .alert i {
            margin-right: 12px;
            font-size: 20px;
            flex-shrink: 0;
        }

        .alert .message {
            flex-grow: 1;
            margin-right: 40px;
            font-weight: 500;
        }

        @keyframes slideDown {
            from {
                transform: translateY(-100%);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        @keyframes slideUp {
            from {
                transform: translateY(0);
                opacity: 1;
            }

            to {
                transform: translateY(-100%);
                opacity: 0;
            }
        }

        /* Custom Dropdown Styling */
        .dropdown-menu {
            border: 0;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }

        /* Badge Styling */
        .nav-item .badge {
            position: absolute;
            top: 5px;
            right: 5px;
            font-size: 10px;
            padding: 3px 5px;
            border-radius: 50%;
        }

        .nav-item {
            position: relative;
        }

        /* Message Dropdown Styling */
        #messages-dropdown {
            width: 300px;
            padding: 0;
        }

        #messages-dropdown .dropdown-header {
            padding: 12px 15px;
            font-weight: bold;
            background-color: #f8f9fa;
        }

        #messages-dropdown .dropdown-item {
            padding: 10px 15px;
            white-space: normal;
        }

        #messages-dropdown .dropdown-item:hover {
            background-color: #f8f9fa;
        }

        #messages-dropdown .user-img img {
            width: 40px;
            height: 40px;
            object-fit: cover;
        }

        #messages-dropdown h6 {
            font-size: 14px;
            margin-bottom: 2px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 200px;
        }

        #messages-dropdown small {
            font-size: 11px;
        }
    </style>

    <!--favicon-->
    <link rel="icon" href="{{ asset('assets/images/favicon.ico') }}" type="image/x-icon">
    <!-- simplebar CSS-->
    <link href="{{ asset('assets/plugins/simplebar/css/simplebar.css') }}" rel="stylesheet" />
    <!-- Bootstrap core CSS-->
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" />
    <!-- animate CSS-->
    <link href="{{ asset('assets/css/animate.css') }}" rel="stylesheet" type="text/css" />
    <!-- Icons CSS-->
    <link href="{{ asset('assets/css/icons.css') }}" rel="stylesheet" type="text/css" />
    <!-- Sidebar CSS-->
    <link href="{{ asset('assets/css/sidebar-menu.css') }}" rel="stylesheet" />
    <!-- Custom Style-->
    <link href="{{ asset('assets/css/app-style.css') }}" rel="stylesheet" />
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css" rel="stylesheet" />
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
    <style>
        /* Custom DataTables styling to match theme */
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter,
        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_paginate {
            margin-bottom: 1rem;
        }

        /* Sidebar submenu styling */
        .sidebar-menu .submenu {
            padding-left: 35px !important;
            background: rgba(0, 0, 0, 0.1) !important;
            margin: 5px 0;
            border-radius: 4px;
        }
        .sidebar-menu .submenu li a {
            padding-left: 35px !important;
            color: rgba(255, 255, 255, 0.8) !important;
            font-size: 13px;
            padding: 8px 15px;
        }
        .sidebar-menu .submenu li a:hover {
            color: #fff !important;
            background: rgba(255, 255, 255, 0.1) !important;
        }
        .sidebar-menu .submenu li.active a {
            color: #fff !important;
            background: rgba(255, 255, 255, 0.1) !important;
        }
        .sidebar-menu .submenu li a i {
            margin-left: 10px;
            font-size: 12px;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button {
            color: rgba(255, 255, 255, 0.8) !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current,
        .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
            color: #333 !important;
        }

        .dataTables_wrapper .dataTables_length select,
        .dataTables_wrapper .dataTables_filter input {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        table.dataTable tbody tr {
            background-color: transparent !important;
        }

        table.dataTable thead th,
        table.dataTable thead td {
            border-bottom: 1px solid rgba(255, 255, 255, 0.2) !important;
        }

        table.dataTable.no-footer {
            border-bottom: 1px solid rgba(255, 255, 255, 0.2) !important;
        }
    </style>
    @yield('styles')

    <!-- Toast Alert Script - Placed in head to be available early -->
    <script>
        // Store session alerts to show after page load
        var sessionAlerts = [];
        @if(session('success'))
            sessionAlerts.push({message: "{{ session('success') }}", type: 'success'});
        @endif

        @if(session('error'))
            sessionAlerts.push({message: "{{ session('error') }}", type: 'error'});
        @endif

        @if(session('warning'))
            sessionAlerts.push({message: "{{ session('warning') }}", type: 'warning'});
        @endif

        @if(session('info'))
            sessionAlerts.push({message: "{{ session('info') }}", type: 'info'});
        @endif
    </script>

    <!-- Custom Dropdown Script -->
    <script>
        // Store the currently open dropdown
        let currentDropdown = null;

        // Function to toggle dropdown
        function toggleDropdown(event, dropdownId) {
            event.preventDefault();
            event.stopPropagation();

            const dropdown = document.getElementById(dropdownId);

            // If clicking the same dropdown that's already open, close it
            if (currentDropdown === dropdown) {
                dropdown.classList.remove('show');
                currentDropdown = null;
                return;
            }

            // Close any other open dropdown
            if (currentDropdown) {
                currentDropdown.classList.remove('show');
            }

            // Open the clicked dropdown
            dropdown.classList.add('show');
            currentDropdown = dropdown;
        }

        // Initialize dropdown functionality when DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            // Close dropdown when clicking outside
            document.addEventListener('click', function(event) {
                if (currentDropdown && !currentDropdown.contains(event.target)) {
                    currentDropdown.classList.remove('show');
                    currentDropdown = null;
                }
            });

            // Close dropdown when pressing escape key
            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape' && currentDropdown) {
                    currentDropdown.classList.remove('show');
                    currentDropdown = null;
                }
            });

            // Prevent dropdown from closing when clicking inside
            document.querySelectorAll('.dropdown-menu').forEach(menu => {
                menu.addEventListener('click', function(event) {
                    event.stopPropagation();
                });
            });
        });
    </script>
</head>

<body class="bg-theme bg-theme1">

    <!-- Start wrapper-->
    <div id="wrapper">

        <!--Start sidebar-wrapper-->
        <div id="sidebar-wrapper" data-simplebar="" data-simplebar-auto-hide="true">
            <div class="brand-logo">
                <a href="{{ url('/dash/dashboard') }}">
                    <img src="{{ asset('assets/images/logo-icon.png') }}" class="logo-icon" alt="logo icon">
                    <h5 class="logo-text">Dashtreme Admin</h5>
                </a>
            </div>
            <ul class="sidebar-menu do-nicescrol">
                <li class="sidebar-header">MAIN NAVIGATION</li>
                <li class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <a href="{{ route('dashboard') }}">
                        <i class="zmdi zmdi-view-dashboard"></i> <span>Dashboard</span>
                    </a>
                </li>

                @if(auth()->user()->role == 3)
                <!-- Parent Attendance Link -->
                <li class="{{ request()->routeIs('attendance.parent.*') ? 'active' : '' }}">
                    <a href="{{ route('attendance.parent.index') }}">
                        <i class="fas fa-calendar-check"></i> <span>Children's Attendance</span>
                    </a>
                </li>
                @else
                <!-- Admin/Teacher Attendance Dropdown -->
                <li class="{{ request()->routeIs('attendance.*') ? 'active' : '' }}">
                    <a href="javaScript:void();" class="waves-effect">
                        <i class="fas fa-calendar-check"></i>
                        <span>Attendance</span>
                        <i class="fas fa-angle-left pull-right"></i>
                    </a>
                    <ul class="sidebar-submenu">
                        <li class="{{ request()->routeIs('attendance.students.*') || request()->is('dash/attendance/students*') ? 'active' : '' }}">
                            <a href="{{ route('attendance.students.index') }}">
                                <i class="fas fa-circle"></i> Student Attendance
                            </a>
                        </li>
                        @if(auth()->user()->role != 2)
                        <li class="{{ request()->routeIs('attendance.teachers.*') || request()->is('dash/attendance/teachers*') ? 'active' : '' }}">
                            <a href="{{ route('attendance.teachers.index') }}">
                                <i class="fas fa-circle"></i> Teacher Attendance
                            </a>
                        </li>
                        @endif
                        <li class="{{ request()->routeIs('attendance.reports') ? 'active' : '' }}">
                            <a href="{{ route('attendance.reports') }}">
                                <i class="fas fa-circle"></i> Attendance Reports
                            </a>
                        </li>
                    </ul>
                </li>
                @endif

                @if(auth()->user()->role != 2 && auth()->user()->role != 3)
                <li class="{{ request()->routeIs('parents.*') ? 'active' : '' }}">
                    <a href="{{ route('parents.index') }}">
                        <i class="zmdi zmdi-accounts-list"></i> <span>Parents</span>
                    </a>
                </li>
                @endif

                @if(auth()->user()->role != 3)
                <li class="{{ request()->routeIs('students.*') ? 'active' : '' }}">
                    <a href="{{ route('students.index') }}">
                        <i class="zmdi zmdi-face"></i> <span>Students</span>
                    </a>
                </li>

                @if(auth()->user()->role != 2)
                <li class="{{ request()->routeIs('teachers.*') ? 'active' : '' }}">
                    <a href="{{ route('teachers.index') }}">
                        <i class="zmdi zmdi-accounts-alt"></i> <span>Teachers</span>
                    </a>
                </li>

                <li class="{{ request()->routeIs('periods.*') ? 'active' : '' }}">
                    <a href="{{ url('/dash/periods') }}">
                        <i class="zmdi zmdi-time"></i> <span>Period Management</span>
                    </a>
                </li>

                <li class="{{ request()->routeIs('classes.*') ? 'active' : '' }}">
                    <a href="{{ url('/dash/classes') }}">
                        <i class="zmdi zmdi-graduation-cap"></i> <span>Class Management</span>
                    </a>
                </li>

                <li class="{{ request()->routeIs('timetable.*') ? 'active' : '' }}">
                    <a href="{{ url('/dash/timetable') }}">
                        <i class="zmdi zmdi-calendar"></i> <span>Timetable</span>
                    </a>
                </li>
                @endif
                @endif

                <li class="{{ request()->routeIs('messages.*') ? 'active' : '' }}">
                    <a href="{{ url('/dash/messages') }}">
                        <i class="zmdi zmdi-email"></i> <span>Messages</span>
                    </a>
                </li>

                @if(auth()->user()->role == 2)
                <li class="{{ request()->routeIs('notes.*') ? 'active' : '' }}">
                    <a href="{{ route('notes.index') }}">
                        <i class="fas fa-sticky-note"></i> <span>Class Notes</span>
                    </a>
                </li>
                @endif

                @if(auth()->user()->role == 3)
                <!-- Parent Tests Link -->
                <li class="{{ request()->routeIs('tests.*') ? 'active' : '' }}">
                    <a href="{{ route('tests.index') }}">
                        <i class="fas fa-file-alt"></i> <span>Children's Tests</span>
                    </a>
                </li>

                <!-- Parent Results Link -->

                @else
                <li class="{{ request()->routeIs('exams.*') || request()->routeIs('tests.*') ? 'active' : '' }}">
                    <a href="javaScript:void();" class="waves-effect">
                        <i class="fas fa-file-alt"></i>
                        <span>Exams & Tests</span>
                        <i class="fas fa-angle-left pull-right"></i>
                    </a>
                    <ul class="sidebar-submenu">
                        @if(auth()->user()->role == 1 || auth()->user()->role == 2)
                        <li class="{{ request()->routeIs('tests.create') ? 'active' : '' }}">
                            <a href="{{ route('tests.create') }}">
                                <i class="fas fa-plus-circle"></i> Create New Test
                            </a>
                        </li>
                        @endif
                        <li class="{{ request()->routeIs('tests.index') ? 'active' : '' }}">
                            <a href="{{ route('tests.index') }}">
                                <i class="fas fa-list"></i> All Tests
                            </a>
                        </li>
                        @if(auth()->user()->role == 1 || auth()->user()->role == 2)
                        <li class="{{ request()->routeIs('test.reports') ? 'active' : '' }}">
                            <a href="{{ route('test.reports') }}">
                                <i class="fas fa-chart-bar"></i> Test Reports
                            </a>
                        </li>
                        @endif
                        @if(auth()->user()->role == 1)
                        <li class="{{ request()->routeIs('exams.create') ? 'active' : '' }}">
                            <a href="{{ route('exams.create') }}">
                                <i class="fas fa-plus-circle"></i> Create New Exam
                            </a>
                        </li>
                        <li class="{{ request()->routeIs('exams.index') ? 'active' : '' }}">
                            <a href="{{ route('exams.index') }}">
                                <i class="fas fa-list"></i> All Exams
                            </a>
                        </li>
                        <li class="{{ request()->routeIs('exams.reports') ? 'active' : '' }}">
                            <a href="{{ route('exams.reports') }}">
                                <i class="fas fa-chart-bar"></i> Exam Reports
                            </a>
                        </li>
                        @endif
                    </ul>
                </li>
                @endif
                <li class="{{ request()->routeIs('datesheets.*') ? 'active' : '' }}">
                    <a href="{{ route('datesheets.index') }}">
                        <i class="fa fa-calendar"></i> <span>Exam Datesheets</span>
                    </a>
                </li>
                <li class="{{ request()->routeIs('exams.results.*') ? 'active' : '' }}">
                    <a href="{{ route('exams.results.index') }}">
                        <i class="fas fa-graduation-cap"></i> <span>Exams Results</span>
                    </a>
                </li>
                @if (auth()->user()->role == 3)
                <li class="{{ request()->routeIs('fees.parent') ? 'active' : '' }}">
                    <a href="{{ route('fees.parent') }}">
                        <i class="zmdi zmdi-money"></i> <span>Fees & Fines</span>
                    </a>
                </li>
                @endif

                @if(auth()->user()->role == 2)
                <li class="{{ request()->routeIs('fines.*') ? 'active' : '' }}">
                    <a href="{{ route('fines.list') }}">
                        <i class="zmdi zmdi-money-off"></i> <span>Fines</span>
                    </a>
                </li>
                @endif

                <li class="{{ request()->routeIs('complaints.*') ? 'active' : '' }}">
                    <a href="{{ route('complaints.index') }}">
                        <i class="zmdi zmdi-comment-alert"></i> <span>Complaints</span>
                    </a>
                </li>


                @if(auth()->user()->role == 1)
                <li
                    class="{{ request()->routeIs('fees.*') || request()->is('dash/fees*') || request()->is('dash/fines*') || request()->is('dash/student-fees*') || request()->is('dash/public-report*') ? 'active' : '' }}">
                    <a href="{{ url('/dash/fees') }}">
                        <i class="zmdi zmdi-money"></i> <span>Fees / Fines</span>
                    </a>
                </li>

                <li class="{{ request()->routeIs('activities.*') ? 'active' : '' }}">
                    <a href="{{ url('/dash/activities') }}">
                        <i class="zmdi zmdi-trending-up"></i> <span>Activity Log</span>
                    </a>
                </li>
                @endif

                <li class="sidebar-header">SETTINGS</li>
                <li class="{{ request()->routeIs('profile.index') ? 'active' : '' }}">
                    <a href="{{ url('/dash/profile') }}">
                        <i class="zmdi zmdi-face"></i> <span>Profile</span>
                    </a>
                </li>

                <li>
                    <a href="{{ url('/dash/logout') }}">
                        <i class="zmdi zmdi-power"></i> <span>Logout</span>
                    </a>
                </li>
            </ul>
        </div>
        <!--End sidebar-wrapper-->

        <!--Start topbar header-->
        <header class="topbar-nav">
            <nav class="navbar navbar-expand fixed-top">
                <ul class="navbar-nav mr-auto align-items-center">
                    <li class="nav-item">
                        <a class="nav-link toggle-menu" href="javascript:void();">
                            <i class="icon-menu menu-icon"></i>
                        </a>
                    </li>
                </ul>

                <ul class="navbar-nav align-items-center right-nav-link">
                    <li class="nav-item">
                        <a class="nav-link" href="#" onclick="toggleDropdown(event, 'messages-dropdown')">
                            <i class="fas fa-envelope"></i>
                            @php
                            $user = Auth::user();
                            $userType = '';
                            switch ($user->role) {
                            case 1: $userType = 'admin'; break;
                            case 2: $userType = 'teacher'; break;
                            case 3: $userType = 'parent'; break;
                            case 4: $userType = 'student'; break;
                            default: $userType = 'unknown';
                            }

                            $unreadMessages = App\Models\Message::where(function ($query) use ($user, $userType) {
                            $query->where('recipient_id', $user->id)
                            ->where('recipient_type', $userType)
                            ->where('is_read', false)
                            ->where('deleted_by_recipient', false);
                            })->count();

                            $recentMessages = App\Models\Message::with('sender')
                            ->where('recipient_id', $user->id)
                            ->where('recipient_type', $userType)
                            ->where('deleted_by_recipient', false)
                            ->orderBy('created_at', 'desc')
                            ->take(5)
                            ->get();
                            @endphp
                            @if($unreadMessages > 0)
                            <span class="badge badge-danger">{{ $unreadMessages }}</span>
                            @endif
                        </a>
                        <div id="messages-dropdown" class="dropdown-menu dropdown-menu-right">
                            <div class="dropdown-header">Messages ({{ $unreadMessages }} unread)</div>
                            <div class="dropdown-divider"></div>
                            @if($recentMessages->count() > 0)
                            @foreach($recentMessages as $message)
                            <a class="dropdown-item" href="{{ route('messages.show', $message->id) }}">
                                <div class="d-flex align-items-center">
                                    <div class="user-img">
                                        @if($message->sender && $message->sender->image)
                                        <img src="{{ Storage::url('profile-images/'.$message->sender->image) }}"
                                            alt="user avatar" class="img-circle" width="40">
                                        @else
                                        <img src="https://ui-avatars.com/api/?name={{ $message->sender ? urlencode($message->sender->name) : 'Unknown' }}&background=random"
                                            alt="user avatar" class="img-circle" width="40">
                                        @endif
                                    </div>
                                    <div class="ml-2">
                                        <h6 class="mb-0">{{ $message->subject }}</h6>
                                        <small class="text-muted">From: {{ $message->sender ? $message->sender->name :
                                            'System' }}</small>
                                        <small class="text-muted d-block">{{ $message->created_at->diffForHumans()
                                            }}</small>
                                    </div>
                                </div>
                            </a>
                            @endforeach
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item text-center" href="{{ route('messages.inbox') }}">View All
                                Messages</a>
                            @else
                            <a class="dropdown-item" href="#">No new messages</a>
                            @endif
                        </div>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" onclick="toggleDropdown(event, 'notifications-dropdown')">
                            <i class="fas fa-bell"></i>
                            @php
                            $pendingComplaints = App\Models\Complaint::where(function($query) use ($user) {
                                if ($user->role == 1) { // Admin sees all pending complaints
                                    $query->where('status', 'pending');
                                } elseif ($user->role == 2) { // Teacher sees complaints against them
                                    $query->where('against_user_id', $user->id)
                                        ->where('status', 'pending');
                                } elseif ($user->role == 3) { // Parent sees their own pending complaints
                                    $query->where('complainant_id', $user->id)
                                        ->where('complainant_type', 'App\Models\User')
                                        ->where('status', 'pending');
                                }
                            })->count();

                            $recentComplaints = App\Models\Complaint::with(['complainant', 'againstUser'])
                                ->where(function($query) use ($user) {
                                    if ($user->role == 1) {
                                        $query->where('status', 'pending');
                                    } elseif ($user->role == 2) {
                                        $query->where('against_user_id', $user->id)
                                            ->where('status', 'pending');
                                    } elseif ($user->role == 3) {
                                        $query->where('complainant_id', $user->id)
                                            ->where('complainant_type', 'App\Models\User')
                                            ->where('status', 'pending');
                                    }
                                })
                                ->orderBy('created_at', 'desc')
                                ->take(5)
                                ->get();
                            @endphp
                            @if($pendingComplaints > 0)
                            <span class="badge badge-warning">{{ $pendingComplaints }}</span>
                            @endif
                        </a>
                        <div id="notifications-dropdown" class="dropdown-menu dropdown-menu-right">
                            <div class="dropdown-header">Pending Complaints ({{ $pendingComplaints }})</div>
                            <div class="dropdown-divider"></div>
                            @if($recentComplaints->count() > 0)
                                @foreach($recentComplaints as $complaint)
                                <a class="dropdown-item" href="{{ route('complaints.show', $complaint->id) }}">
                                    <div class="d-flex align-items-center">
                                        <div class="ml-2">
                                            <h6 class="mb-0">{{ $complaint->subject }}</h6>
                                            <small class="text-muted">From: {{ $complaint->complainant->name }}</small>
                                            @if($complaint->againstUser)
                                            <small class="text-muted d-block">Against: {{ $complaint->againstUser->name }}</small>
                                            @endif
                                            <small class="text-muted d-block">{{ $complaint->created_at->diffForHumans() }}</small>
                                        </div>
                                    </div>
                                </a>
                                @endforeach
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item text-center" href="{{ route('complaints.index') }}">View All Complaints</a>
                            @else
                                <a class="dropdown-item" href="#">No pending complaints</a>
                            @endif
                        </div>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" onclick="toggleDropdown(event, 'profile-dropdown')">
                            <span class="user-profile">
                                @if(auth()->user()->image)
                                <img src="{{ asset('storage/profile-images/'.auth()->user()->image) }}"
                                    class="img-circle" alt="user avatar">
                                @else
                                <img src="https://ui-avatars.com/api/?name={{ auth()->user()->name ?? 'Guest User' }}&background=random"
                                    class="img-circle" alt="user avatar">
                                @endif
                            </span>
                        </a>
                        <div id="profile-dropdown" class="dropdown-menu dropdown-menu-right">
                            <div class="dropdown-item user-details">
                                <div class="media">
                                    <div class="avatar">
                                        @if(auth()->user()->image)
                                        <img class="align-self-start mr-3"
                                            src="{{ asset('storage/profile-images/'.auth()->user()->image) }}"
                                            alt="user avatar">
                                        @else
                                        <img class="align-self-start mr-3"
                                            src="https://ui-avatars.com/api/?name={{ auth()->user()->name ?? 'Guest User' }}&background=random"
                                            alt="user avatar">
                                        @endif
                                    </div>
                                    <div class="media-body">
                                        <h6 class="mt-2 user-title">{{ auth()->user()->name ?? 'Guest User' }}</h6>
                                        <p class="user-subtitle">{{ auth()->user()->email ?? 'guest@example.com' }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="{{ route('messages.inbox') }}"><i
                                    class="fas fa-envelope mr-2"></i> Inbox</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="{{ url('/dash/profile') }}"><i class="fas fa-cog mr-2"></i>
                                Setting</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="{{ url('/dash/logout') }}">
                                <i class="fas fa-power-off mr-2"></i> Logout
                            </a>
                        </div>
                    </li>
                </ul>
            </nav>
        </header>
        <!--End topbar header-->

        <div class="clearfix"></div>

        <!-- Alert Container -->
        <div class="alert-container"></div>

        <div class="content-wrapper">
            <div class="container-fluid">
                @yield('content')
            </div>
            <!-- End container-fluid-->
        </div>
        <!--End content-wrapper-->

        <!--Start Back To Top Button-->
        <a href="javaScript:void();" class="back-to-top"><i class="fas fa-angle-double-up"></i> </a>
        <!--End Back To Top Button-->

        <!--Start footer-->
        <footer class="footer">
            <div class="container">
                <div class="text-center">
                    Copyright © {{ date('Y') }} Dashtreme Admin
                </div>
            </div>
        </footer>
        <!--End footer-->

        <!--start color switcher-->
        <div class="right-sidebar">
            <div class="switcher-icon">
                <i class="zmdi zmdi-settings zmdi-hc-spin"></i>
            </div>
            <div class="right-sidebar-content">
                <p class="mb-0">Gaussion Texture</p>
                <hr>
                <ul class="switcher">
                    <li id="theme1"></li>
                    <li id="theme2"></li>
                    <li id="theme3"></li>
                    <li id="theme4"></li>
                    <li id="theme5"></li>
                    <li id="theme6"></li>
                </ul>
                <p class="mb-0">Gradient Background</p>
                <hr>
                <ul class="switcher">
                    <li id="theme7"></li>
                    <li id="theme8"></li>
                    <li id="theme9"></li>
                    <li id="theme10"></li>
                    <li id="theme11"></li>
                    <li id="theme12"></li>
                    <li id="theme13"></li>
                    <li id="theme14"></li>
                    <li id="theme15"></li>
                </ul>
            </div>
        </div>
        <!--end color switcher-->

    </div>
    <!--End wrapper-->

    <!-- Bootstrap core JavaScript-->
    <script src="{{ asset('assets/js/jquery.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <!-- simplebar js -->
    <script src="{{ asset('assets/plugins/simplebar/js/simplebar.js') }}"></script>
    <!-- sidebar-menu js -->
    <script src="{{ asset('assets/js/sidebar-menu.js') }}"></script>
    <!-- Custom scripts -->
    <script src="{{ asset('assets/js/app-script.js') }}"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
    <!-- Initialize all datatables -->
    <script>
        // Initialize DataTables
        function initializeDataTables() {
            $('.datatable').each(function() {
                var table = $(this);

                // Check if DataTable is already initialized
                if ($.fn.DataTable.isDataTable(table)) {
                    return; // Skip if already initialized
                }

                // Ensure table has proper structure
                if (!table.find('thead').length || !table.find('tbody').length) {
                    console.warn('Table missing required thead or tbody elements');
                    return;
                }

                var hasData = table.find('tbody tr').length > 0;

                // Add basic wrapper and styling for all tables
                if (!table.parent().hasClass('dataTables_wrapper')) {
                    var wrapper = $('<div class="dataTables_wrapper"></div>');
                    table.wrap(wrapper);
                }

                // Only initialize DataTables if there is data
                if (hasData) {
                    try {
                        // Add DataTables classes for consistent styling
                        table.addClass('dataTable no-footer');
                        table.find('thead').addClass('dataTables-header');
                        table.find('tbody').addClass('dataTables-body');

                        // Initialize DataTable with default settings
                        var dataTable = table.DataTable({
                            "responsive": true,
                            "autoWidth": false,
                            "language": {
                                "lengthMenu": "Show _MENU_ entries",
                                "zeroRecords": "No records found",
                                "info": "Showing page _PAGE_ of _PAGES_",
                                "infoEmpty": "No records available",
                                "infoFiltered": "(filtered from _MAX_ total records)"
                            },
                            "columnDefs": [
                                {
                                    "targets": "_all",
                                    "defaultContent": " "
                                }
                            ]
                        });
                    } catch (error) {
                        console.error('Error initializing DataTable:', error);
                    }
                } else {
                    // For empty tables, add empty state message
                    var info = $('<div class="dataTables_info">No records available</div>');
                    table.after(info);

                    // Add empty state styling
                    table.addClass('table table-striped');
                    table.find('thead').addClass('thead-dark');
                }
            });
        }

        // Initialize Sidebar
        function initializeSidebar() {
            // Remove any existing click handlers
            $('.toggle-menu').off('click');
            $(document).off('click');
            $('#sidebar-wrapper').off('click');

            // Toggle sidebar on menu icon click
            $('.toggle-menu').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                $('#wrapper').toggleClass('toggled');
            });

            // Close sidebar when clicking outside
            $(document).on('click', function(e) {
                if (!$(e.target).closest('#sidebar-wrapper, .toggle-menu').length) {
                    $('#wrapper').removeClass('toggled');
                }
            });

            // Prevent sidebar from closing when clicking inside it
            $('#sidebar-wrapper').on('click', function(e) {
                e.stopPropagation();
            });

            // Initialize SimpleBar
            if (typeof SimpleBar !== 'undefined') {
                new SimpleBar(document.getElementById('sidebar-wrapper'));
            }
        }

        // Initialize Theme
        function initializeTheme() {
            const savedTheme = localStorage.getItem('selectedTheme');
            if (savedTheme) {
                applyTheme(savedTheme);
            }

            // Theme switcher click handlers
            $('.switcher li').each(function() {
                $(this).on('click', function() {
                    const themeId = $(this).attr('id');
                    applyTheme(themeId);
                });
            });

            // Close right sidebar (theme switcher) when clicking outside
            $(document).on('click', function(e) {
                if (!$(e.target).closest('.right-sidebar, .switcher-icon').length) {
                    $('.right-sidebar').removeClass('right-toggled');
                }
            });
        }

        // Function to apply theme
        function applyTheme(themeId) {
            $('body').removeClass(function(index, className) {
                return (className.match(/(^|\s)bg-theme\d+/g) || []).join(' ');
            });
            $('body').addClass('bg-theme bg-' + themeId);
            localStorage.setItem('selectedTheme', themeId);
        }

        // Initialize everything when document is ready
        $(document).ready(function() {
            try {
                initializeDataTables();
                initializeSidebar();
                initializeTheme();
            } catch (error) {
                console.error('Error during initialization:', error);
            }
        });

        // Backup initialization in case document ready doesn't fire
        window.addEventListener('load', function() {
            try {
                initializeDataTables();
                initializeSidebar();
                initializeTheme();
            } catch (error) {
                console.error('Error during backup initialization:', error);
            }
        });
    </script>

    <!-- Handle AJAX alerts -->
    <script>
        let alertTimeout;

        function showAlert(message, type) {
            // Clear any existing timeout
            if (alertTimeout) {
                clearTimeout(alertTimeout);
            }

            // Remove any existing alerts
            $('.alert').remove();

            // Get icon based on type
            let icon = '';
            switch(type) {
                case 'success':
                    icon = '<i class="zmdi zmdi-check-circle"></i>';
                    break;
                case 'danger':
                    icon = '<i class="zmdi zmdi-close-circle"></i>';
                    break;
                case 'warning':
                    icon = '<i class="zmdi zmdi-alert-circle"></i>';
                    break;
                case 'info':
                    icon = '<i class="zmdi zmdi-info"></i>';
                    break;
            }

            const alertHtml = `
                <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                    ${icon}
                    <div class="message">${message}</div>
                    <button type="button" class="close"  style='margin-top: 17px;' data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            `;

            // Add new alert
            $('.alert-container').html(alertHtml);

            // Set timeout to hide alert after 3 seconds
            alertTimeout = setTimeout(() => {
                const alert = $('.alert');
                alert.addClass('hide');
                setTimeout(() => {
                    alert.remove();
                }, 400);
            }, 3000);

            // Handle close button click
            $('.alert .close').on('click', function() {
                clearTimeout(alertTimeout);
                const alert = $(this).closest('.alert');
                alert.addClass('hide');
                setTimeout(() => {
                    alert.remove();
                }, 400);
            });
        }

        // Show session alerts on page load
        $(document).ready(function() {
            @if(session('success'))
                showAlert("{{ session('success') }}", 'success');
            @endif

            @if(session('error'))
                showAlert("{{ session('error') }}", 'danger');
            @endif

            @if(session('warning'))
                showAlert("{{ session('warning') }}", 'warning');
            @endif

            @if(session('info'))
                showAlert("{{ session('info') }}", 'info');
            @endif
        });

        // Handle AJAX responses
        $(document).ajaxComplete(function(event, xhr, settings) {
            try {
                const response = JSON.parse(xhr.responseText);
                if (response.success) {
                    showAlert(response.success, 'success');
                }
                if (response.error) {
                    showAlert(response.error, 'danger');
                }
                if (response.warning) {
                    showAlert(response.warning, 'warning');
                }
                if (response.info) {
                    showAlert(response.info, 'info');
                }
            } catch (e) {
                // Not a JSON response, ignore
            }
        });
    </script>

    @push('styles')
    <style>
        .nav-item {
            position: relative;
        }

        .dropdown-menu {
            display: none;
            position: absolute;
            right: 0;
            top: 100%;
            min-width: 200px;
            background-color: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            margin-top: 0.5rem;
            z-index: 1000;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.2s ease-in-out;
        }

        .dropdown-menu.show {
            display: block;
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .dropdown-item {
            color: rgba(255, 255, 255, 0.8);
            padding: 0.5rem 1rem;
            display: block;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .dropdown-item:hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: #fff;
        }

        .dropdown-divider {
            border-top-color: rgba(255, 255, 255, 0.1);
            margin: 0.5rem 0;
        }

        .dropdown-header {
            color: rgba(255, 255, 255, 0.6);
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
        }

        .user-profile {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            overflow: hidden;
            cursor: pointer;
        }

        .user-profile img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .user-details .avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            overflow: hidden;
        }

        .user-details .avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .user-title {
            color: #fff;
            margin-bottom: 0;
            font-size: 0.875rem;
        }

        .user-subtitle {
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.75rem;
            margin-bottom: 0;
        }
    </style>
    @endpush

    @push('scripts')
    <script>
        // Remove the dropdown functionality from here since it's now in the head
    </script>
    @endpush

</body>

</html>
