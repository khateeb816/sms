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
        // Get the authenticated user
        $user = auth()->user();

        if ($user->role == 2) { // Teacher Role
            // Get teacher's assigned classes
            $teacherClasses = ClassRoom::where('teacher_id', $user->id)
                ->where('is_active', true)
                ->get();

            $assignedClasses = $teacherClasses->count();

            // Get total students in teacher's classes
            $totalStudentsAssigned = DB::table('users')
                ->join('class_student', 'users.id', '=', 'class_student.student_id')
                ->whereIn('class_student.class_id', $teacherClasses->pluck('id'))
                ->where('users.role', 4) // Student role
                ->where('users.status', 'active')
                ->distinct()
                ->count('users.id');

            // Calculate average students per class
            $averageStudentsPerClass = $assignedClasses > 0 ? round($totalStudentsAssigned / $assignedClasses) : 0;

            // Get teacher's timetable
            $teacherTimetable = $this->getTeacherTimetable($user->id);

            return view('backend.pages.dashboard.index', compact(
                'assignedClasses',
                'totalStudentsAssigned',
                'averageStudentsPerClass',
                'teacherTimetable'
            ));
        }

        // Get counts for dashboard (for admin)
        $totalStudents = User::where('role', 4)->where('status', 'active')->count();
        $totalTeachers = User::where('role', 2)->where('status', 'active')->count();
        $totalClasses = ClassRoom::where('is_active', true)->count();
        $totalParents = User::where('role', 3)->where('status', 'active')->count();

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

        // Get attendance data
        $attendanceData = AttendanceController::getDashboardAttendanceData();

        // Get recent activities
        $recentActivities = Activity::with('user')
            ->orderBy('created_at', 'desc')
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

        // Get recent messages
        $recentMessages = DB::table('messages')
            ->join('users as sender', 'messages.sender_id', '=', 'sender.id')
            ->select('messages.*', 'sender.name as sender_name')
            ->orderBy('messages.created_at', 'desc')
            ->take(5)
            ->get();

        return view('backend.pages.dashboard.index', compact(
            'totalStudents',
            'totalTeachers',
            'totalClasses',
            'totalParents',
            'totalFees',
            'tuitionFees',
            'examFees',
            'transportFees',
            'otherFees',
            'recentPayments',
            'attendanceData',
            'recentActivities',
            'recentMessages'
        ));
    }

    /**
     * Get the teacher's timetable
     *
     * @param int $teacherId
     * @return array
     */
    private function getTeacherTimetable($teacherId)
    {
        $timetable = [];

        // Get all periods first
        $periods = DB::table('periods')
            ->orderBy('start_time')
            ->get();

        // Initialize timetable with all periods and empty slots
        foreach ($periods as $period) {
            $timeSlot = Carbon::parse($period->start_time)->format('H:i') . ' - ' .
                Carbon::parse($period->end_time)->format('H:i');

            $timetable[$timeSlot] = [
                'monday' => '-',
                'tuesday' => '-',
                'wednesday' => '-',
                'thursday' => '-',
                'friday' => '-'
            ];
        }

        // Get all timetable entries for this teacher
        $entries = DB::table('timetables')
            ->join('class_rooms', 'timetables.class_id', '=', 'class_rooms.id')
            ->where('timetables.teacher_id', $teacherId)
            ->select('timetables.*', 'class_rooms.name as class_name')
            ->get();

        // Fill in the booked slots
        foreach ($entries as $entry) {
            $timeSlot = Carbon::parse($entry->start_time)->format('H:i') . ' - ' .
                Carbon::parse($entry->end_time)->format('H:i');

            $day = strtolower($entry->day_of_week);
            $timetable[$timeSlot][$day] = $entry->class_name . ' (' . $entry->subject . ')';
        }

        // Sort by time
        ksort($timetable);

        return $timetable;
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
