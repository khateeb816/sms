<?php

namespace App\Services;

use App\Models\Activity;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ActivityService
{
    /**
     * Log an activity
     *
     * @param string $description The activity description
     * @param int|null $userId The user ID (defaults to authenticated user)
     * @return Activity
     */
    public static function log($description, $userId = null)
    {
        $userId = $userId ?? (Auth::check() ? Auth::id() : null);

        return Activity::create([
            'user_id' => $userId,
            'description' => $description,
            'ip_address' => Request::ip(),
        ]);
    }

    /**
     * Log a fee-related activity
     *
     * @param string $action The action performed
     * @param string $feeType The type of fee
     * @param float $amount The fee amount
     * @param int $studentId The student ID
     * @return Activity
     */
    public static function logFeeActivity($action, $feeType, $amount, $studentId)
    {
        $description = "{$action} {$feeType} fee of PKR " . number_format($amount) . " for student #{$studentId}";
        return self::log($description);
    }

    /**
     * Log a fine-related activity
     *
     * @param string $action The action performed
     * @param string $fineType The type of fine
     * @param float $amount The fine amount
     * @param int $studentId The student ID
     * @return Activity
     */
    public static function logFineActivity($action, $fineType, $amount, $studentId)
    {
        $description = "{$action} {$fineType} fine of PKR " . number_format($amount) . " for student #{$studentId}";
        return self::log($description);
    }

    /**
     * Log a student-related activity
     *
     * @param string $action The action performed
     * @param string $studentName The student name
     * @param int $studentId The student ID
     * @return Activity
     */
    public static function logStudentActivity($action, $studentName, $studentId)
    {
        $user = auth()->user();

        Activity::create([
            'user_id' => $user->id,
            'action' => "$action student: $studentName",
            'action_model' => 'Student',
            'action_id' => $studentId
        ]);
    }

    /**
     * Log a teacher-related activity
     *
     * @param string $action The action performed
     * @param string $teacherName The teacher name
     * @param int $teacherId The teacher ID
     * @return Activity
     */
    public static function logTeacherActivity($action, $teacherName, $teacherId)
    {
        $user = auth()->user();

        Activity::create([
            'user_id' => $user->id,
            'action' => "$action teacher: $teacherName",
            'action_model' => 'Teacher',
            'action_id' => $teacherId
        ]);
    }

    /**
     * Log a class-related activity
     *
     * @param string $action The action performed
     * @param string $className The class name
     * @param int $classId The class ID
     * @return Activity
     */
    public static function logClassActivity($action, $className, $classId)
    {
        $description = "{$action} class {$className} (#{$classId})";
        return self::log($description);
    }

    /**
     * Log a message-related activity
     *
     * @param string $action The action performed
     * @param string $subject The message subject
     * @param string $recipient The recipient(s)
     * @return Activity
     */
    public static function logMessageActivity($action, $subject, $recipient)
    {
        $description = "{$action} message '{$subject}' to {$recipient}";
        return self::log($description);
    }

    /**
     * Log a parent-related activity
     *
     * @param string $action The action performed
     * @param string $parentName The parent name
     * @param int $parentId The parent ID
     * @return Activity
     */
    public static function logParentActivity($action, $parentName, $parentId)
    {
        $user = auth()->user();

        Activity::create([
            'user_id' => $user->id,
            'action' => "$action parent: $parentName",
            'action_model' => 'Parent',
            'action_id' => $parentId
        ]);
    }

    /**
     * Log a period-related activity
     *
     * @param string $action The action performed
     * @param string $periodName The period name
     * @param int $periodId The period ID
     * @return Activity
     */
    public static function logPeriodActivity($action, $periodName, $periodId)
    {
        $description = "{$action} period {$periodName} (#{$periodId})";
        return self::log($description);
    }

    /**
     * Log a timetable-related activity
     *
     * @param string $action The action performed
     * @param string $className The class name
     * @param string $day The day of the week
     * @param int $timetableId The timetable ID
     * @return Activity
     */
    public static function logTimetableActivity($action, $className, $day, $timetableId)
    {
        $description = "{$action} timetable for {$className} on {$day} (#{$timetableId})";
        return self::log($description);
    }

    /**
     * Log a system activity
     *
     * @param string $action The action performed
     * @param string $module The system module
     * @return Activity
     */
    public static function logSystemActivity($action, $module)
    {
        $description = "{$action} in {$module} module";
        return self::log($description);
    }

    /**
     * Log a report-related activity
     *
     * @param string $reportType The type of report (fees, fines, or both)
     * @param string $startDate The start date of the report
     * @param string $endDate The end date of the report
     * @param string $filters Additional filters applied (optional)
     * @return Activity
     */
    public static function logReportActivity($reportType, $startDate, $endDate, $filters = '')
    {
        $description = "Generated {$reportType} report from {$startDate} to {$endDate}";

        if (!empty($filters)) {
            $description .= " with filters: {$filters}";
        }

        return self::log($description);
    }

    /**
     * Log attendance activity
     *
     * @param string $action
     * @param string $userName
     * @param int $userId
     * @param string $type
     * @return void
     */
    public static function logAttendanceActivity($action, $userName, $userId, $type = 'student')
    {
        $description = "$action attendance for $type: $userName";
        return self::log($description);
    }
}
