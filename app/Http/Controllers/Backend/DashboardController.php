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
            // Get teacher's assigned classes from timetable
            $teacherClassIds = DB::table('timetables')
                ->where('teacher_id', $user->id)
                ->distinct()
                ->pluck('class_id');

            $teacherClasses = ClassRoom::whereIn('id', $teacherClassIds)
                ->where('is_active', true)
                ->get();

            $assignedClasses = $teacherClasses->count();

            // Get total students in teacher's classes
            $totalStudentsAssigned = DB::table('users')
                ->join('class_student', 'users.id', '=', 'class_student.student_id')
                ->whereIn('class_student.class_id', $teacherClassIds)
                ->where('users.role', 4) // Student role
                ->where('users.status', 'active')
                ->distinct()
                ->count('users.id');

            // Calculate average students per class
            $averageStudentsPerClass = $assignedClasses > 0 ? round($totalStudentsAssigned / $assignedClasses) : 0;

            // Get teacher's timetable
            $teacherTimetable = $this->getTeacherTimetable($user->id);

            // Get attendance data for teacher's classes
            $startDate = Carbon::now()->subDays(30);
            $endDate = Carbon::now();

            // Get student attendance records for teacher's classes
            $studentAttendanceRecords = DB::table('attendances')
                ->join('users', 'attendances.user_id', '=', 'users.id')
                ->join('class_student', 'users.id', '=', 'class_student.student_id')
                ->whereIn('class_student.class_id', $teacherClassIds)
                ->where('attendances.attendee_type', 'student')
                ->whereBetween('attendances.date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                ->get();

            // Calculate total possible attendance records (students * days)
            $totalPossibleRecords = $totalStudentsAssigned * 30; // 30 days
            $totalActualRecords = $studentAttendanceRecords->count();

            // Calculate student attendance percentages based on total possible records
            $studentPresent = $totalPossibleRecords > 0 ? round(($studentAttendanceRecords->where('status', 'present')->count() / $totalPossibleRecords) * 100) : 0;
            $studentAbsent = $totalPossibleRecords > 0 ? round(($studentAttendanceRecords->where('status', 'absent')->count() / $totalPossibleRecords) * 100) : 0;
            $studentLate = $totalPossibleRecords > 0 ? round(($studentAttendanceRecords->where('status', 'late')->count() / $totalPossibleRecords) * 100) : 0;
            $studentLeave = $totalPossibleRecords > 0 ? round(($studentAttendanceRecords->where('status', 'leave')->count() / $totalPossibleRecords) * 100) : 0;

            // Calculate average attendance for the class overview
            $presentCount = $studentAttendanceRecords->where('status', 'present')->count();
            $averageAttendance = $totalPossibleRecords > 0 ? round(($presentCount / $totalPossibleRecords) * 100) : 0;

            // Get teacher's own attendance records
            $teacherAttendanceRecords = DB::table('attendances')
                ->where('user_id', $user->id)
                ->where('attendee_type', 'teacher')
                ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                ->get();

            $totalTeacherRecords = $teacherAttendanceRecords->count();

            // Calculate teacher attendance percentages
            $teacherPresent = $totalTeacherRecords > 0 ? round(($teacherAttendanceRecords->where('status', 'present')->count() / $totalTeacherRecords) * 100) : 0;
            $teacherAbsent = $totalTeacherRecords > 0 ? round(($teacherAttendanceRecords->where('status', 'absent')->count() / $totalTeacherRecords) * 100) : 0;
            $teacherLate = $totalTeacherRecords > 0 ? round(($teacherAttendanceRecords->where('status', 'late')->count() / $totalTeacherRecords) * 100) : 0;
            $teacherLeave = $totalTeacherRecords > 0 ? round(($teacherAttendanceRecords->where('status', 'leave')->count() / $totalTeacherRecords) * 100) : 0;

            // Get today's classes count
            $today = strtolower(Carbon::now()->format('l'));
            $todayClasses = collect($teacherTimetable)->map(function($slots) use ($today) {
                return $slots[$today] ?? '-';
            })->filter(function($class) {
                return $class != '-';
            })->count();

            return view('backend.pages.dashboard.index', compact(
                'assignedClasses',
                'totalStudentsAssigned',
                'averageStudentsPerClass',
                'teacherTimetable',
                'studentPresent',
                'studentAbsent',
                'studentLate',
                'studentLeave',
                'teacherPresent',
                'teacherAbsent',
                'teacherLate',
                'teacherLeave',
                'todayClasses',
                'averageAttendance'
            ));
        }

        // Get counts for dashboard (for admin)
        $totalStudents = User::where('role', 4)->where('status', 'active')->count();
        $totalTeachers = User::where('role', 2)->where('status', 'active')->count();
        $totalClasses = ClassRoom::where('is_active', true)->count();
        $totalParents = User::where('role', 3)->where('status', 'active')->count();

        // Get attendance data for the last 30 days
        $startDate = Carbon::now()->subDays(30);
        $endDate = Carbon::now();

        // Get student attendance records
        $studentAttendanceRecords = DB::table('attendances')
            ->where('attendee_type', 'student')
            ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->get();

        // Calculate total possible attendance records
        $totalPossibleRecords = $totalStudents * 30; // 30 days
        $totalActualRecords = $studentAttendanceRecords->count();

        // Calculate student attendance percentages
        $studentPresent = $totalActualRecords > 0 ? round(($studentAttendanceRecords->where('status', 'present')->count() / $totalActualRecords) * 100) : 0;
        $studentAbsent = $totalActualRecords > 0 ? round(($studentAttendanceRecords->where('status', 'absent')->count() / $totalActualRecords) * 100) : 0;
        $studentLate = $totalActualRecords > 0 ? round(($studentAttendanceRecords->where('status', 'late')->count() / $totalActualRecords) * 100) : 0;
        $studentLeave = $totalActualRecords > 0 ? round(($studentAttendanceRecords->where('status', 'leave')->count() / $totalActualRecords) * 100) : 0;

        // Get teacher attendance records
        $teacherAttendanceRecords = DB::table('attendances')
            ->where('attendee_type', 'teacher')
            ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->get();

        $totalTeacherRecords = $teacherAttendanceRecords->count();

        // Calculate teacher attendance percentages
        $teacherPresent = $totalTeacherRecords > 0 ? round(($teacherAttendanceRecords->where('status', 'present')->count() / $totalTeacherRecords) * 100) : 0;
        $teacherAbsent = $totalTeacherRecords > 0 ? round(($teacherAttendanceRecords->where('status', 'absent')->count() / $totalTeacherRecords) * 100) : 0;
        $teacherLate = $totalTeacherRecords > 0 ? round(($teacherAttendanceRecords->where('status', 'late')->count() / $totalTeacherRecords) * 100) : 0;
        $teacherLeave = $totalTeacherRecords > 0 ? round(($teacherAttendanceRecords->where('status', 'leave')->count() / $totalTeacherRecords) * 100) : 0;

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
            'recentMessages',
            'studentPresent',
            'studentAbsent',
            'studentLate',
            'studentLeave',
            'teacherPresent',
            'teacherAbsent',
            'teacherLate',
            'teacherLeave'
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
