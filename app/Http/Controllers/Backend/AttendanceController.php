<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\ClassRoom;
use App\Models\Attendance;
use Carbon\Carbon;
use App\Services\ActivityService;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    /**
     * Display the student attendance page.
     *
     * @return \Illuminate\Http\Response
     */
    public function studentsIndex(Request $request)
    {
        // Get the authenticated user
        $user = auth()->user();

        // Get classes based on user role
        if ($user->role == 2) { // Teacher role
            // Get classes where the teacher has timetable entries
            $classes = ClassRoom::where('is_active', true)
                ->whereIn('id', function ($query) use ($user) {
                    $query->select('class_id')
                        ->from('timetables')
                        ->where('teacher_id', $user->id)
                        ->distinct();
                })
                ->get();
        } else {
            // Admin sees all active classes
            $classes = ClassRoom::where('is_active', true)->get();
        }

        $selectedClass = $request->class_id ? ClassRoom::findOrFail($request->class_id) : null;
        $selectedDate = $request->date ? Carbon::parse($request->date) : Carbon::today();

        // If teacher is trying to access a class they don't teach
        if ($user->role == 2 && $selectedClass && !$classes->contains('id', $selectedClass->id)) {
            return redirect()->route('attendance.students.index')
                ->with('error', 'You do not have permission to view attendance for this class.');
        }

        $students = [];
        if ($selectedClass) {
            $students = User::where('role', 4) // Role 4 is for students
                ->where('status', 'active')
                ->whereHas('classes', function ($query) use ($selectedClass) {
                    $query->where('class_rooms.id', $selectedClass->id);
                })
                ->orderBy('name')
                ->get();

            // Get existing attendance records for the selected date and class
            $attendanceRecords = Attendance::where('date', $selectedDate->format('Y-m-d'))
                ->where('attendee_type', 'student')
                ->whereIn('user_id', $students->pluck('id'))
                ->get()
                ->keyBy('user_id');

            // Add attendance status to each student
            foreach ($students as $student) {
                $student->attendance = $attendanceRecords->get($student->id);
            }
        }

        return view('backend.pages.attendance.students', compact('classes', 'selectedClass', 'selectedDate', 'students'));
    }

    /**
     * Display the teacher attendance page.
     *
     * @return \Illuminate\Http\Response
     */
    public function teachersIndex(Request $request)
    {
        $selectedDate = $request->date ? Carbon::parse($request->date) : Carbon::today();

        $teachers = User::where('role', 2) // Role 2 is for teachers
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        // Get existing attendance records for the selected date
        $attendanceRecords = Attendance::where('date', $selectedDate->format('Y-m-d'))
            ->where('attendee_type', 'teacher')
            ->whereIn('user_id', $teachers->pluck('id'))
            ->get()
            ->keyBy('user_id');

        // Add attendance status to each teacher
        foreach ($teachers as $teacher) {
            $teacher->attendance = $attendanceRecords->get($teacher->id);
        }

        return view('backend.pages.attendance.teachers', compact('selectedDate', 'teachers'));
    }

    /**
     * Mark attendance for students.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function markStudentAttendance(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:class_rooms,id',
            'date' => 'required|date',
            'attendance' => 'required|array',
            'attendance.*.user_id' => 'required|exists:users,id',
            'attendance.*.status' => 'required|in:present,absent,late,leave',
        ]);

        $date = Carbon::parse($request->date)->format('Y-m-d');
        $class = ClassRoom::findOrFail($request->class_id);

        foreach ($request->attendance as $data) {
            $student = User::findOrFail($data['user_id']);
            Attendance::updateOrCreate(
                [
                    'user_id' => $data['user_id'],
                    'date' => $date,
                    'attendee_type' => 'student',
                ],
                [
                    'status' => $data['status'],
                    'remarks' => $data['remarks'] ?? null,
                ]
            );

            // Log for each student
            ActivityService::logAttendanceActivity('Marked', $student->name, $student->id, 'student');
        }

        // Log the activity
        ActivityService::log('Marked student attendance for class ' . $class->name);

        return redirect()->route('attendance.students.index', ['class_id' => $request->class_id, 'date' => $date])
            ->with('success', 'Student attendance marked successfully.');
    }

    /**
     * Mark attendance for teachers.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function markTeacherAttendance(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'attendance' => 'required|array',
            'attendance.*.user_id' => 'required|exists:users,id',
            'attendance.*.status' => 'required|in:present,absent,late,leave',
        ]);

        $date = Carbon::parse($request->date)->format('Y-m-d');

        foreach ($request->attendance as $data) {
            $teacher = User::findOrFail($data['user_id']);
            Attendance::updateOrCreate(
                [
                    'user_id' => $data['user_id'],
                    'date' => $date,
                    'attendee_type' => 'teacher',
                ],
                [
                    'status' => $data['status'],
                    'remarks' => $data['remarks'] ?? null,
                ]
            );

            // Log for each teacher
            ActivityService::logAttendanceActivity('Marked', $teacher->name, $teacher->id, 'teacher');
        }

        // Log the activity
        ActivityService::log('Marked teacher attendance for ' . $date);

        return redirect()->route('attendance.teachers.index', ['date' => $date])
            ->with('success', 'Teacher attendance marked successfully.');
    }

    /**
     * Display attendance reports.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function reports(Request $request)
    {
        // Get the authenticated user
        $user = auth()->user();

        $type = $request->type ?? 'student';
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now()->endOfMonth();
        $userId = $request->user_id ?? null;
        $classId = $request->class_id ?? null;
        $status = $request->status ?? null;

        // Build the query
        $query = Attendance::whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->where('attendee_type', $type)
            ->with('user');

        // If user is a teacher
        if ($user->role == 2) {
            if ($type === 'teacher') {
                // Teachers can only see their own attendance
                $query->where('user_id', $user->id);
                $userId = $user->id; // Force user filter to logged-in teacher
            } else {
                // For student attendance, get the teacher's assigned classes from timetable
                $teacherClassIds = DB::table('timetables')
                    ->where('teacher_id', $user->id)
                    ->distinct()
                    ->pluck('class_id');

                // Get students from these classes
                $studentIds = DB::table('class_student')
                    ->whereIn('class_id', $teacherClassIds)
                    ->pluck('student_id');

                $query->whereIn('user_id', $studentIds);

                if ($classId && !$teacherClassIds->contains($classId)) {
                    // If teacher tries to access unauthorized class
                    return redirect()->route('attendance.reports')
                        ->with('error', 'You do not have permission to view this class attendance.');
                }
            }
        }

        if ($userId) {
            $query->where('user_id', $userId);
        }

        if ($classId) {
            // For student attendance, filter by class
            if ($type === 'student') {
                $studentIds = DB::table('class_student')
                    ->where('class_id', $classId)
                    ->pluck('student_id');
                $query->whereIn('user_id', $studentIds);
            }
        }

        if ($status) {
            $query->where('status', $status);
        }

        // Get paginated results
        $attendances = $query->orderBy('date', 'desc')->paginate(15)->withQueryString();

        // Get all records for summary statistics
        $allRecords = $query->get();

        // Group by date for summary
        $summary = [
            'present' => $allRecords->where('status', 'present')->count(),
            'absent' => $allRecords->where('status', 'absent')->count(),
            'late' => $allRecords->where('status', 'late')->count(),
            'leave' => $allRecords->where('status', 'leave')->count(),
        ];

        // Get users for filter based on role
        if ($type === 'student') {
            if ($user->role == 2) {
                // For teachers, only show students from their assigned classes
                $teacherClassIds = DB::table('timetables')
                    ->where('teacher_id', $user->id)
                    ->distinct()
                    ->pluck('class_id');

                $users = User::where('role', 4)
                    ->where('status', 'active')
                    ->whereHas('classes', function ($query) use ($teacherClassIds) {
                        $query->whereIn('class_rooms.id', $teacherClassIds);
                    })
                    ->orderBy('name')
                    ->get();

                $classes = ClassRoom::whereIn('id', $teacherClassIds)
                    ->where('is_active', true)
                    ->get();
            } else {
                // Admin sees all
                $users = User::where('role', 4)
                    ->where('status', 'active')
                    ->orderBy('name')
                    ->get();
                $classes = ClassRoom::where('is_active', true)->get();
            }
        } else {
            if ($user->role == 2) {
                // Teachers only see themselves in teacher list
                $users = User::where('id', $user->id)->get();
            } else {
                // Admin sees all teachers
                $users = User::where('role', 2)
                    ->where('status', 'active')
                    ->orderBy('name')
                    ->get();
            }
            $classes = collect(); // Empty collection for teachers
        }

        // Log the report generation activity
        $logMessage = 'Generated ' . $type . ' attendance report';

        if ($startDate && $endDate) {
            $logMessage .= ' for period ' . $startDate->format('d M, Y') . ' to ' . $endDate->format('d M, Y');
        }

        if ($userId) {
            $userName = $users->where('id', $userId)->first()->name ?? 'Unknown';
            $logMessage .= ' for ' . ($type === 'student' ? 'student' : 'teacher') . ': ' . $userName;
        }

        if ($classId && $type === 'student') {
            $className = $classes->where('id', $classId)->first()->name ?? 'Unknown';
            $logMessage .= ' in class: ' . $className;
        }

        if ($status) {
            $logMessage .= ' with status: ' . ucfirst($status);
        }

        ActivityService::log($logMessage);

        return view('backend.pages.attendance.reports', compact(
            'type',
            'startDate',
            'endDate',
            'userId',
            'classId',
            'status',
            'attendances',
            'summary',
            'users',
            'classes'
        ));
    }

    /**
     * Log when a user prints an attendance report.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logPrintActivity(Request $request)
    {
        $type = $request->type ?? 'student';
        $startDate = $request->start_date ? Carbon::parse($request->start_date)->format('d M, Y') : null;
        $endDate = $request->end_date ? Carbon::parse($request->end_date)->format('d M, Y') : null;
        $userId = $request->user_id ?? null;
        $classId = $request->class_id ?? null;
        $status = $request->status ?? null;

        // Build log message
        $logMessage = 'Printed ' . $type . ' attendance report';

        // Add filter details to log message
        if ($startDate && $endDate) {
            $logMessage .= ' for period ' . $startDate . ' to ' . $endDate;
        }

        if ($userId) {
            $user = User::find($userId);
            if ($user) {
                $logMessage .= ' for ' . ($type === 'student' ? 'student' : 'teacher') . ': ' . $user->name;
            }
        }

        if ($classId && $type === 'student') {
            $class = ClassRoom::find($classId);
            if ($class) {
                $logMessage .= ' in class: ' . $class->name;
            }
        }

        if ($status) {
            $logMessage .= ' with status: ' . ucfirst($status);
        }

        ActivityService::log($logMessage);

        return response()->json(['success' => true, 'message' => 'Print activity logged successfully']);
    }

    /**
     * Show the form for editing the specified attendance record.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $attendance = Attendance::findOrFail($id);
        $user = User::findOrFail($attendance->user_id);

        if ($attendance->attendee_type === 'student') {
            $classes = ClassRoom::where('is_active', true)->get();
            $userClass = $user->classes->first();

            return view('backend.pages.attendance.edit', compact('attendance', 'user', 'classes', 'userClass'));
        } else {
            return view('backend.pages.attendance.edit', compact('attendance', 'user'));
        }
    }

    /**
     * Update the specified attendance record in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:present,absent,late,leave',
            'remarks' => 'nullable|string|max:255',
        ]);

        $attendance = Attendance::findOrFail($id);
        $attendance->status = $request->status;
        $attendance->remarks = $request->remarks;
        $attendance->save();

        $user = User::findOrFail($attendance->user_id);
        $userType = $attendance->attendee_type === 'student' ? 'student' : 'teacher';

        // Log the activity
        ActivityService::log('Updated ' . $userType . ' attendance for ' . $user->name . ' on ' . $attendance->date->format('d M, Y'));

        if ($userType === 'student') {
            return redirect()->route('attendance.students.index', ['date' => $attendance->date->format('Y-m-d')])
                ->with('success', 'Student attendance updated successfully.');
        } else {
            return redirect()->route('attendance.teachers.index', ['date' => $attendance->date->format('Y-m-d')])
                ->with('success', 'Teacher attendance updated successfully.');
        }
    }

    /**
     * Remove the specified attendance record from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $attendance = Attendance::findOrFail($id);
        $date = $attendance->date->format('Y-m-d');
        $userType = $attendance->attendee_type;
        $user = User::findOrFail($attendance->user_id);

        $attendance->delete();

        // Log the activity
        ActivityService::log('Deleted ' . $userType . ' attendance for ' . $user->name . ' on ' . $attendance->date->format('d M, Y'));

        if ($userType === 'student') {
            return redirect()->route('attendance.students.index', ['date' => $date])
                ->with('success', 'Student attendance deleted successfully.');
        } else {
            return redirect()->route('attendance.teachers.index', ['date' => $date])
                ->with('success', 'Teacher attendance deleted successfully.');
        }
    }

    /**
     * Get attendance data for dashboard.
     *
     * @return array
     */
    public static function getDashboardAttendanceData()
    {
        $startDate = Carbon::now()->subDays(30);
        $endDate = Carbon::now();

        // Get all attendance records for the last 30 days
        $attendanceRecords = Attendance::whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->get();

        // Student attendance records
        $studentRecords = $attendanceRecords->where('attendee_type', 'student');
        $totalStudentRecords = $studentRecords->count() ?: 1; // Avoid division by zero

        // Teacher attendance records
        $teacherRecords = $attendanceRecords->where('attendee_type', 'teacher');
        $totalTeacherRecords = $teacherRecords->count() ?: 1; // Avoid division by zero

        // Calculate student attendance percentages
        $studentPresent = round(($studentRecords->where('status', 'present')->count() / $totalStudentRecords) * 100);
        $studentAbsent = round(($studentRecords->where('status', 'absent')->count() / $totalStudentRecords) * 100);
        $studentLate = round(($studentRecords->where('status', 'late')->count() / $totalStudentRecords) * 100);
        $studentLeave = round(($studentRecords->where('status', 'leave')->count() / $totalStudentRecords) * 100);

        // Calculate teacher attendance percentages
        $teacherPresent = round(($teacherRecords->where('status', 'present')->count() / $totalTeacherRecords) * 100);
        $teacherAbsent = round(($teacherRecords->where('status', 'absent')->count() / $totalTeacherRecords) * 100);
        $teacherLate = round(($teacherRecords->where('status', 'late')->count() / $totalTeacherRecords) * 100);
        $teacherLeave = round(($teacherRecords->where('status', 'leave')->count() / $totalTeacherRecords) * 100);

        // Calculate trends (comparing with previous 30 days)
        $previousStartDate = Carbon::now()->subDays(60);
        $previousEndDate = Carbon::now()->subDays(31);

        $previousRecords = Attendance::whereBetween('date', [$previousStartDate->format('Y-m-d'), $previousEndDate->format('Y-m-d')])
            ->get();

        // Previous student records
        $prevStudentRecords = $previousRecords->where('attendee_type', 'student');
        $totalPrevStudentRecords = $prevStudentRecords->count() ?: 1;

        // Previous teacher records
        $prevTeacherRecords = $previousRecords->where('attendee_type', 'teacher');
        $totalPrevTeacherRecords = $prevTeacherRecords->count() ?: 1;

        // Calculate student trends
        $prevStudentPresent = round(($prevStudentRecords->where('status', 'present')->count() / $totalPrevStudentRecords) * 100);
        $prevStudentAbsent = round(($prevStudentRecords->where('status', 'absent')->count() / $totalPrevStudentRecords) * 100);
        $prevStudentLate = round(($prevStudentRecords->where('status', 'late')->count() / $totalPrevStudentRecords) * 100);
        $prevStudentLeave = round(($prevStudentRecords->where('status', 'leave')->count() / $totalPrevStudentRecords) * 100);

        // Calculate teacher trends
        $prevTeacherPresent = round(($prevTeacherRecords->where('status', 'present')->count() / $totalPrevTeacherRecords) * 100);
        $prevTeacherAbsent = round(($prevTeacherRecords->where('status', 'absent')->count() / $totalPrevTeacherRecords) * 100);
        $prevTeacherLate = round(($prevTeacherRecords->where('status', 'late')->count() / $totalPrevTeacherRecords) * 100);
        $prevTeacherLeave = round(($prevTeacherRecords->where('status', 'leave')->count() / $totalPrevTeacherRecords) * 100);

        // Return attendance data
        return [
            // Overall attendance
            'overall' => round(($attendanceRecords->where('status', 'present')->count() / max(1, $attendanceRecords->count())) * 100),
            'student' => $studentPresent,
            'teacher' => $teacherPresent,

            // Legacy keys for backward compatibility
            'present' => $studentPresent,
            'absent' => $studentAbsent,
            'leave' => $studentLeave,
            'late' => $studentLate,

            // Student attendance data
            'student_present' => $studentPresent,
            'student_absent' => $studentAbsent,
            'student_late' => $studentLate,
            'student_leave' => $studentLeave,

            // Student trends
            'student_present_trend' => $studentPresent - $prevStudentPresent,
            'student_absent_trend' => $studentAbsent - $prevStudentAbsent,
            'student_late_trend' => $studentLate - $prevStudentLate,
            'student_leave_trend' => $studentLeave - $prevStudentLeave,

            // Teacher attendance data
            'teacher_present' => $teacherPresent,
            'teacher_absent' => $teacherAbsent,
            'teacher_late' => $teacherLate,
            'teacher_leave' => $teacherLeave,

            // Teacher trends
            'teacher_present_trend' => $teacherPresent - $prevTeacherPresent,
            'teacher_absent_trend' => $teacherAbsent - $prevTeacherAbsent,
            'teacher_late_trend' => $teacherLate - $prevTeacherLate,
            'teacher_leave_trend' => $teacherLeave - $prevTeacherLeave,
        ];
    }
}
