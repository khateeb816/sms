<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>@yield('title', 'Dashtreme Admin - Laravel Dashboard')</title>
    <!-- loader-->
    <link href="{{ asset('assets/css/pace.min.css') }}" rel="stylesheet" />
    <script src="{{ asset('assets/js/pace.min.js') }}"></script>
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
    <style>
        /* Custom DataTables styling to match theme */
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter,
        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_processing,
        .dataTables_wrapper .dataTables_paginate {
            color: rgba(255, 255, 255, 0.8) !important;
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
</head>

<body class="bg-theme bg-theme1">

    <!-- Start wrapper-->
    <div id="wrapper">

        <!--Start sidebar-wrapper-->
        <div id="sidebar-wrapper" data-simplebar="" data-simplebar-auto-hide="true">
            <div class="brand-logo">
                <a href="{{ url('/admin/dashboard') }}">
                    <img src="{{ asset('assets/images/logo-icon.png') }}" class="logo-icon" alt="logo icon">
                    <h5 class="logo-text">Dashtreme Admin</h5>
                </a>
            </div>
            <ul class="sidebar-menu do-nicescrol">
                <li class="sidebar-header">MAIN NAVIGATION</li>
                <li class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <a href="{{ url('/admin/dashboard') }}">
                        <i class="zmdi zmdi-view-dashboard"></i> <span>Dashboard</span>
                    </a>
                </li>

                <li class="{{ request()->routeIs('parents.*') ? 'active' : '' }}">
                    <a href="{{ url('/admin/parents') }}">
                        <i class="zmdi zmdi-accounts-list"></i> <span>Parents</span>
                    </a>
                </li>

                <li class="{{ request()->routeIs('teachers.*') ? 'active' : '' }}">
                    <a href="{{ url('/admin/teachers') }}">
                        <i class="zmdi zmdi-accounts-alt"></i> <span>Teachers</span>
                    </a>
                </li>

                <li class="{{ request()->routeIs('periods.*') ? 'active' : '' }}">
                    <a href="{{ url('/admin/periods') }}">
                        <i class="zmdi zmdi-time"></i> <span>Period Management</span>
                    </a>
                </li>

                <li class="{{ request()->routeIs('classes.*') ? 'active' : '' }}">
                    <a href="{{ url('/admin/classes') }}">
                        <i class="zmdi zmdi-graduation-cap"></i> <span>Class Management</span>
                    </a>
                </li>

                <li class="{{ request()->routeIs('timetable.*') ? 'active' : '' }}">
                    <a href="{{ url('/admin/timetable') }}">
                        <i class="zmdi zmdi-calendar"></i> <span>Timetable</span>
                    </a>
                </li>

                <li class="{{ request()->routeIs('messages.*') ? 'active' : '' }}">
                    <a href="{{ url('/admin/messages') }}">
                        <i class="zmdi zmdi-email"></i> <span>Messages</span>
                    </a>
                </li>

                <li class="{{ request()->routeIs('fees.*') ? 'active' : '' }}">
                    <a href="{{ url('/admin/fees') }}">
                        <i class="zmdi zmdi-money"></i> <span>Fees / Fines</span>
                    </a>
                </li>

                <li class="sidebar-header">SETTINGS</li>
                <li class="{{ request()->routeIs('profile') ? 'active' : '' }}">
                    <a href="{{ url('/admin/profile') }}">
                        <i class="zmdi zmdi-face"></i> <span>Profile</span>
                    </a>
                </li>

                <li>
                    <a href="{{ url('/admin/logout') }}"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
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
                    <li class="nav-item">
                        <form class="search-bar">
                            <input type="text" class="form-control" placeholder="Enter keywords">
                            <a href="javascript:void();"><i class="icon-magnifier"></i></a>
                        </form>
                    </li>
                </ul>

                <ul class="navbar-nav align-items-center right-nav-link">
                    <li class="nav-item dropdown-lg">
                        <a class="nav-link dropdown-toggle dropdown-toggle-nocaret waves-effect" data-toggle="dropdown"
                            href="javascript:void();">
                            <i class="fa fa-envelope-open-o"></i></a>
                    </li>
                    <li class="nav-item dropdown-lg">
                        <a class="nav-link dropdown-toggle dropdown-toggle-nocaret waves-effect" data-toggle="dropdown"
                            href="javascript:void();">
                            <i class="fa fa-bell-o"></i></a>
                    </li>
                    <li class="nav-item language">
                        <a class="nav-link dropdown-toggle dropdown-toggle-nocaret waves-effect" data-toggle="dropdown"
                            href="javascript:void();"><i class="fa fa-flag"></i></a>
                        <ul class="dropdown-menu dropdown-menu-right">
                            <li class="dropdown-item"> <i class="flag-icon flag-icon-gb mr-2"></i> English</li>
                            <li class="dropdown-item"> <i class="flag-icon flag-icon-fr mr-2"></i> French</li>
                            <li class="dropdown-item"> <i class="flag-icon flag-icon-cn mr-2"></i> Chinese</li>
                            <li class="dropdown-item"> <i class="flag-icon flag-icon-de mr-2"></i> German</li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link dropdown-toggle dropdown-toggle-nocaret" data-toggle="dropdown" href="#">
                            <span class="user-profile"><img
                                    src="https://ui-avatars.com/api/?name={{ auth()->user()->name ?? 'Guest User' }}&background=random"
                                    class="img-circle" alt="user avatar"></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-right">
                            <li class="dropdown-item user-details">
                                <a href="javaScript:void();">
                                    <div class="media">
                                        <div class="avatar"><img class="align-self-start mr-3"
                                                src="https://ui-avatars.com/api/?name={{ auth()->user()->name ?? 'Guest User' }}&background=random"
                                                alt="user avatar"></div>
                                        <div class="media-body">
                                            <h6 class="mt-2 user-title">{{ auth()->user()->name ?? 'Guest User' }}</h6>
                                            <p class="user-subtitle">{{ auth()->user()->email ?? 'guest@example.com' }}
                                            </p>
                                        </div>
                                    </div>
                                </a>
                            </li>
                            <li class="dropdown-divider"></li>
                            <li class="dropdown-item"><i class="icon-envelope mr-2"></i> Inbox</li>
                            <li class="dropdown-divider"></li>
                            <li class="dropdown-item"><i class="icon-wallet mr-2"></i> Account</li>
                            <li class="dropdown-divider"></li>
                            <li class="dropdown-item"><i class="icon-settings mr-2"></i> Setting</li>
                            <li class="dropdown-divider"></li>
                            <li class="dropdown-item">
                                <a href="{{ url('/admin/logout') }}"
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="icon-power mr-2"></i> Logout
                                </a>
                                <form id="logout-form" action="{{ url('/admin/logout') }}" method="POST"
                                    style="display: none;">
                                    @csrf
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </nav>
        </header>
        <!--End topbar header-->

        <div class="clearfix"></div>

        <div class="content-wrapper">
            <div class="container-fluid">
                @yield('content')
            </div>
            <!-- End container-fluid-->
        </div>
        <!--End content-wrapper-->

        <!--Start Back To Top Button-->
        <a href="javaScript:void();" class="back-to-top"><i class="fa fa-angle-double-up"></i> </a>
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
    <script src="{{ asset('assets/js/popper.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>

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
        $(document).ready(function() {
            $('.datatable').DataTable({
                "responsive": true,
                "autoWidth": false,
                "language": {
                    "lengthMenu": "Show _MENU_ entries",
                    "zeroRecords": "No records found",
                    "info": "Showing page _PAGE_ of _PAGES_",
                    "infoEmpty": "No records available",
                    "infoFiltered": "(filtered from _MAX_ total records)"
                }
            });
        });
    </script>

    <!-- Theme persistence script -->
    <script>
        $(document).ready(function() {
            // Switcher icon click handler
            $('.switcher-icon').on('click', function() {
                $('.right-sidebar').toggleClass('show');
            });

            // Close sidebar when clicking outside
            $(document).on('click', function(e) {
                if (!$(e.target).closest('.right-sidebar, .switcher-icon').length) {
                    $('.right-sidebar').removeClass('show');
                }
            });

            // Function to apply theme
            function applyTheme(themeId) {
                // Remove any existing theme classes
                $('body').removeClass(function(index, className) {
                    return (className.match(/(^|\s)bg-theme\d+/g) || []).join(' ');
                });
                // Add the new theme class
                $('body').addClass('bg-theme bg-' + themeId);
                localStorage.setItem('selectedTheme', themeId);
            }

            // Check localStorage for saved theme
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
        });
    </script>

</body>

</html>