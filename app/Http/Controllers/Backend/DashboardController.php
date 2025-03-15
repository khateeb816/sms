<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\ClassRoom;
use App\Models\Fee;
use App\Models\Fine;
use App\Models\Activity;
use App\Http\Controllers\Backend\AttendanceController;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display the dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Get counts for dashboard
        $totalStudents = User::where('role', 4)->where('status', 'active')->count();
        $totalTeachers = User::where('role', 2)->where('status', 'active')->count();
        $totalClasses = ClassRoom::where('is_active', true)->count();

        // Get fee data
        $fees = Fee::all();
        $totalFees = $fees->sum('amount');
        $tuitionFees = $fees->where('fee_type', 'tuition')->sum('amount');
        $examFees = $fees->where('fee_type', 'exam')->sum('amount');
        $transportFees = $fees->where('fee_type', 'transport')->sum('amount');
        $otherFees = $fees->where('fee_type', 'other')->sum('amount');

        // Get recent payments
        $recentPayments = Fee::with('student')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Get attendance data using our new method
        $attendanceData = AttendanceController::getDashboardAttendanceData();

        // Get recent activities
        $recentActivities = Activity::orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($activity) {
                $icon = 'zmdi-info-outline';

                if (strpos($activity->description, 'attendance') !== false) {
                    $icon = 'zmdi-calendar-check';
                } elseif (strpos($activity->description, 'fee') !== false) {
                    $icon = 'zmdi-money';
                } elseif (strpos($activity->description, 'student') !== false) {
                    $icon = 'zmdi-accounts';
                } elseif (strpos($activity->description, 'teacher') !== false) {
                    $icon = 'zmdi-accounts-list';
                }

                return [
                    'title' => 'Activity Log',
                    'description' => $activity->description,
                    'icon' => $icon,
                    'time' => $activity->created_at,
                    'user' => $activity->user
                ];
            });

        return view('backend.pages.dashboard.index', compact(
            'totalStudents',
            'totalTeachers',
            'totalClasses',
            'totalFees',
            'tuitionFees',
            'examFees',
            'transportFees',
            'otherFees',
            'recentPayments',
            'attendanceData',
            'recentActivities'
        ));
    }

    /**
     * Show the icons page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function icons()
    {
        return view('backend.icons');
    }

    /**
     * Show the forms page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function forms()
    {
        return view('backend.forms');
    }

    /**
     * Show the tables page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function tables()
    {
        return view('backend.tables');
    }

    /**
     * Show the calendar page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function calendar()
    {
        return view('backend.calendar');
    }

    /**
     * Show the profile page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function profile()
    {
        return view('backend.profile');
    }

    public function parents()
    {
        return view('backend.pages.parent.index');
    }
}
