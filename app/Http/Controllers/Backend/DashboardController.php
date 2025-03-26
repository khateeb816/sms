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
use App\Models\Exam;
use App\Models\Test;
use App\Models\Attendance;
use App\Models\ExamResult;
use App\Services\ActivityService;
use Illuminate\Support\Facades\Auth;

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
        $user = Auth::user();
        $data = [];

        if ($user->role === 1) { // Admin
            $data = $this->getAdminDashboardData();
        } elseif ($user->role === 2) { // Teacher
            $data = $this->getTeacherDashboardData($user);
        } elseif ($user->role === 3) { // Parent
            $data = $this->getParentDashboardData($user);
        }

        return view('backend.pages.dashboard.index', $data);
    }

    private function getAdminDashboardData()
    {
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

        // Get recent activities
        $recentActivities = Activity::with('user')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Get recent messages
        $recentMessages = DB::table('messages')
            ->join('users as sender', 'messages.sender_id', '=', 'sender.id')
            ->select('messages.*', 'sender.name as sender_name')
            ->orderBy('messages.created_at', 'desc')
            ->take(5)
            ->get();

        // Initialize teacher-related variables for admin dashboard
        $assignedClasses = 0;
        $totalStudentsAssigned = 0;
        $averageStudentsPerClass = 0;
        $teacherTimetable = [];
        $todayClasses = 0;
        $averageAttendance = 0;

        return [
            'totalStudents' => $totalStudents,
            'totalTeachers' => $totalTeachers,
            'totalClasses' => $totalClasses,
            'totalParents' => $totalParents,
            'totalFees' => $totalFees,
            'tuitionFees' => $tuitionFees,
            'examFees' => $examFees,
            'transportFees' => $transportFees,
            'otherFees' => $otherFees,
            'recentPayments' => $recentPayments,
            'recentActivities' => $recentActivities,
            'recentMessages' => $recentMessages,
            'studentPresent' => $studentPresent,
            'studentAbsent' => $studentAbsent,
            'studentLate' => $studentLate,
            'studentLeave' => $studentLeave,
            'teacherPresent' => $teacherPresent,
            'teacherAbsent' => $teacherAbsent,
            'teacherLate' => $teacherLate,
            'teacherLeave' => $teacherLeave,
            'assignedClasses' => $assignedClasses,
            'totalStudentsAssigned' => $totalStudentsAssigned,
            'averageStudentsPerClass' => $averageStudentsPerClass,
            'teacherTimetable' => $teacherTimetable,
            'todayClasses' => $todayClasses,
            'averageAttendance' => $averageAttendance
        ];
    }

    private function getTeacherDashboardData($user)
    {
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

        return [
            'assignedClasses' => $assignedClasses,
            'totalStudentsAssigned' => $totalStudentsAssigned,
            'averageStudentsPerClass' => $averageStudentsPerClass,
            'teacherTimetable' => $teacherTimetable,
            'studentPresent' => $studentPresent,
            'studentAbsent' => $studentAbsent,
            'studentLate' => $studentLate,
            'studentLeave' => $studentLeave,
            'teacherPresent' => $teacherPresent,
            'teacherAbsent' => $teacherAbsent,
            'teacherLate' => $teacherLate,
            'teacherLeave' => $teacherLeave,
            'todayClasses' => $todayClasses,
            'averageAttendance' => $averageAttendance
        ];
    }

    private function getParentDashboardData($user)
    {
        // Get all children of the parent using parent_id from users table
        $children = DB::table('users')
            ->where('users.parent_id', $user->id)
            ->select(
                'users.id',
                'users.name',
                'users.email'
            )
            ->get();

        // Get attendance overview for each child
        $attendanceOverview = [];
        foreach ($children as $child) {
            $monthlyAttendance = Attendance::where('user_id', $child->id)
                ->where('attendee_type', 'student')
                ->whereMonth('date', Carbon::now()->month)
                ->whereYear('date', Carbon::now()->year)
                ->get();

            $attendanceOverview[$child->id] = [
                'present' => $monthlyAttendance->where('status', 'present')->count(),
                'absent' => $monthlyAttendance->where('status', 'absent')->count(),
                'late' => $monthlyAttendance->where('status', 'late')->count(),
                'total' => $monthlyAttendance->count(),
            ];
        }

        // Get unpaid fees for all children
        $unpaidFees = Fee::whereIn('student_id', $children->pluck('id'))
            ->where('status', 'unpaid')
            ->with('student')
            ->orderBy('due_date')
            ->limit(5)
            ->get();

        // Get unpaid fines for all children
        $unpaidFines = Fine::whereIn('student_id', $children->pluck('id'))
            ->where('status', 'unpaid')
            ->with('student')
            ->orderBy('due_date')
            ->limit(5)
            ->get();

        // Get upcoming exams for all children's classes
        $upcomingExams = Exam::whereIn('class_id', $children->pluck('id'))
            ->where('exam_date', '>=', Carbon::now())
            ->orderBy('exam_date')
            ->limit(5)
            ->get();

        // Get recent test results for all children
        $recentResults = ExamResult::whereIn('student_id', $children->pluck('id'))
            ->with(['exam', 'student'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Initialize teacher-related variables with default values
        $assignedClasses = 0;
        $totalStudentsAssigned = 0;
        $averageStudentsPerClass = 0;
        $teacherTimetable = [];
        $todayClasses = 0;
        $averageAttendance = 0;

        // Initialize admin-related variables with default values
        $totalStudents = 0;
        $totalTeachers = 0;
        $totalClasses = 0;
        $totalParents = 0;
        $totalFees = 0;
        $tuitionFees = 0;
        $examFees = 0;
        $transportFees = 0;
        $otherFees = 0;
        $recentPayments = collect([]);
        $recentActivities = [];  // Initialize as array instead of collection
        $recentMessages = collect([]);
        $studentPresent = 0;
        $studentAbsent = 0;
        $studentLate = 0;
        $studentLeave = 0;
        $teacherPresent = 0;
        $teacherAbsent = 0;
        $teacherLate = 0;
        $teacherLeave = 0;

        return [
            'children' => $children,
            'attendanceOverview' => $attendanceOverview,
            'unpaidFees' => $unpaidFees,
            'unpaidFines' => $unpaidFines,
            'upcomingExams' => $upcomingExams,
            'recentResults' => $recentResults,
            'totalChildren' => $children->count(),
            'totalUnpaidFees' => $unpaidFees->count(),
            'totalUnpaidFines' => $unpaidFines->count(),
            // Add teacher-related variables
            'assignedClasses' => $assignedClasses,
            'totalStudentsAssigned' => $totalStudentsAssigned,
            'averageStudentsPerClass' => $averageStudentsPerClass,
            'teacherTimetable' => $teacherTimetable,
            'todayClasses' => $todayClasses,
            'averageAttendance' => $averageAttendance,
            // Add admin-related variables
            'totalStudents' => $totalStudents,
            'totalTeachers' => $totalTeachers,
            'totalClasses' => $totalClasses,
            'totalParents' => $totalParents,
            'totalFees' => $totalFees,
            'tuitionFees' => $tuitionFees,
            'examFees' => $examFees,
            'transportFees' => $transportFees,
            'otherFees' => $otherFees,
            'recentPayments' => $recentPayments,
            'recentActivities' => $recentActivities,
            'recentMessages' => $recentMessages,
            'studentPresent' => $studentPresent,
            'studentAbsent' => $studentAbsent,
            'studentLate' => $studentLate,
            'studentLeave' => $studentLeave,
            'teacherPresent' => $teacherPresent,
            'teacherAbsent' => $teacherAbsent,
            'teacherLate' => $teacherLate,
            'teacherLeave' => $teacherLeave
        ];
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
