<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\ClassRoom;
use App\Models\Fee;
use App\Models\Fine;
use App\Models\Activity;
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
     * Show the dashboard page with dynamic data.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // Get counts for dashboard stats
        $totalStudents = User::where('role', 4)->count(); // Role 4 is for students
        $totalTeachers = User::where('role', 2)->count(); // Role 2 is for teachers
        $totalClasses = ClassRoom::count();

        // Get fee statistics
        $totalFees = Fee::where('status', 'paid')->sum('amount');
        $tuitionFees = Fee::where('status', 'paid')
            ->where('fee_type', 'tuition')
            ->sum('amount');
        $examFees = Fee::where('status', 'paid')
            ->where('fee_type', 'exam')
            ->sum('amount');
        $transportFees = Fee::where('status', 'paid')
            ->where('fee_type', 'transport')
            ->sum('amount');
        $otherFees = Fee::where('status', 'paid')
            ->whereNotIn('fee_type', ['tuition', 'exam', 'transport'])
            ->sum('amount');

        // Get attendance data (placeholder - you would need an actual attendance model)
        // This is simulated data since we don't have the actual attendance model
        $attendanceData = [
            'present' => 92,
            'absent' => 5,
            'leave' => 3,
            'overall' => 95.8,
            'teacher' => 98.3,
            'student' => 94.6,
        ];

        // Get recent activities from the database
        $dbActivities = Activity::with('user')
            ->orderBy('created_at', 'desc')
            ->take(4)
            ->get();

        // Format activities for display
        $recentActivities = [];
        foreach ($dbActivities as $activity) {
            $recentActivities[] = [
                'type' => $this->determineActivityType($activity->description),
                'icon' => $this->getActivityIcon($activity->description),
                'title' => $this->getActivityTitle($activity->description),
                'description' => $activity->description,
                'time' => $activity->created_at,
                'user' => $activity->user,
            ];
        }

        // If we don't have enough activities, add some recent fee payments
        if (count($recentActivities) < 4) {
            $recentFeePayments = Fee::with('student')
                ->where('status', 'paid')
                ->orderBy('payment_date', 'desc')
                ->take(4 - count($recentActivities))
                ->get();

            foreach ($recentFeePayments as $payment) {
                $recentActivities[] = [
                    'type' => 'fee_payment',
                    'icon' => 'zmdi-money',
                    'title' => 'Fee payment received',
                    'description' => $payment->student->name . ' paid PKR ' . number_format($payment->amount) . ' for ' . $payment->fee_type . ' fees',
                    'time' => $payment->payment_date,
                    'user' => $payment->student,
                ];
            }
        }

        // Sort activities by time
        usort($recentActivities, function ($a, $b) {
            return strtotime($b['time']) - strtotime($a['time']);
        });

        // Get recent fee payments for the table
        $recentPayments = Fee::with('student')
            ->orderBy('payment_date', 'desc')
            ->take(5)
            ->get();

        // Return view with all the data
        return view('backend.pages.dashboard.index', compact(
            'totalStudents',
            'totalTeachers',
            'totalClasses',
            'totalFees',
            'tuitionFees',
            'examFees',
            'transportFees',
            'otherFees',
            'attendanceData',
            'recentActivities',
            'recentPayments'
        ));
    }

    /**
     * Determine the type of activity based on its description.
     *
     * @param string $description
     * @return string
     */
    private function determineActivityType($description)
    {
        if (stripos($description, 'fee') !== false) {
            return 'fee_payment';
        } elseif (stripos($description, 'student') !== false) {
            return 'student';
        } elseif (stripos($description, 'teacher') !== false) {
            return 'teacher';
        } elseif (stripos($description, 'class') !== false) {
            return 'class';
        } elseif (stripos($description, 'message') !== false) {
            return 'message';
        } elseif (stripos($description, 'exam') !== false) {
            return 'exam';
        } else {
            return 'system';
        }
    }

    /**
     * Get the appropriate icon for an activity based on its description.
     *
     * @param string $description
     * @return string
     */
    private function getActivityIcon($description)
    {
        if (stripos($description, 'fee') !== false) {
            return 'zmdi-money';
        } elseif (stripos($description, 'student') !== false) {
            return 'zmdi-account';
        } elseif (stripos($description, 'teacher') !== false) {
            return 'zmdi-accounts-list';
        } elseif (stripos($description, 'class') !== false) {
            return 'zmdi-graduation-cap';
        } elseif (stripos($description, 'message') !== false) {
            return 'zmdi-email';
        } elseif (stripos($description, 'exam') !== false) {
            return 'zmdi-calendar-check';
        } else {
            return 'zmdi-settings';
        }
    }

    /**
     * Get a title for an activity based on its description.
     *
     * @param string $description
     * @return string
     */
    private function getActivityTitle($description)
    {
        if (stripos($description, 'fee') !== false) {
            return 'Fee Activity';
        } elseif (stripos($description, 'student') !== false) {
            return 'Student Activity';
        } elseif (stripos($description, 'teacher') !== false) {
            return 'Teacher Activity';
        } elseif (stripos($description, 'class') !== false) {
            return 'Class Activity';
        } elseif (stripos($description, 'message') !== false) {
            return 'Message Activity';
        } elseif (stripos($description, 'exam') !== false) {
            return 'Exam Activity';
        } else {
            return 'System Activity';
        }
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
