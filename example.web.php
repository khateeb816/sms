<?php

use App\Http\Controllers\Backend\Auth\ForgotPasswordController;
use App\Http\Controllers\Backend\Auth\LoginController;
use App\Http\Controllers\Backend\Auth\RegisterController;
use App\Http\Controllers\Backend\Auth\ResetPasswordController;
use App\Http\Controllers\Backend\ClassController;
use App\Http\Controllers\Backend\DashboardController;
use App\Http\Controllers\Backend\FeeController;
use App\Http\Controllers\Backend\MessageController;
use App\Http\Controllers\Backend\ParentController;
use App\Http\Controllers\Backend\PeriodController;
use App\Http\Controllers\Backend\ProfileController;
use App\Http\Controllers\Backend\StudentController;
use App\Http\Controllers\Backend\TeacherController;
use App\Http\Controllers\Backend\TimetableController;
use App\Http\Controllers\Backend\ActivityController;
use App\Http\Controllers\Backend\AttendanceController;
use Illuminate\Support\Facades\Route;




/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect('/dash/login');
});
Route::get('/dash', function () {
    return redirect('/dash/login');
});

Route::prefix('dash')->group(function () {
    // Authentication Routes
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Registration Routes  
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);

    // Password Reset Routes
    Route::get('/password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/password/reset', [ForgotPasswordController::class, 'reset'])->name('password.update');

    // Protected Routes
    Route::middleware(['auth'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Parents Routes
        Route::get('/parents', [ParentController::class, 'index'])->name('parents.index');
        Route::get('/parents/create', [ParentController::class, 'create'])->name('parents.create');
        Route::post('/parents', [ParentController::class, 'store'])->name('parents.store');
        Route::get('/parents/{parent}', [ParentController::class, 'show'])->name('parents.show');
        Route::get('/parents/{parent}/edit', [ParentController::class, 'edit'])->name('parents.edit');
        Route::put('/parents/{parent}', [ParentController::class, 'update'])->name('parents.update');
        Route::delete('/parents/{parent}', [ParentController::class, 'destroy'])->name('parents.destroy');
        Route::get('/parents/{parent}/add-child', [ParentController::class, 'addChildForm'])->name('parents.add-child');
        Route::post('/parents/{parent}/add-child', [ParentController::class, 'addChild'])->name('parents.store-child');
        Route::delete('/parents/{parent}/remove-child/{child}', [ParentController::class, 'removeChild'])->name('parents.remove-child');

        // Students Routes
        Route::get('/students', [StudentController::class, 'index'])->name('students.index');
        Route::get('/students/create', [StudentController::class, 'create'])->name('students.create');
        Route::post('/students', [StudentController::class, 'store'])->name('students.store');
        Route::get('/students/{student}', [StudentController::class, 'show'])->name('students.show');
        Route::get('/students/{student}/edit', [StudentController::class, 'edit'])->name('students.edit');
        Route::put('/students/{student}', [StudentController::class, 'update'])->name('students.update');
        Route::delete('/students/{student}', [StudentController::class, 'destroy'])->name('students.destroy');
        Route::put('/students/{student}/update-picture', [StudentController::class, 'updateProfilePicture'])->name('students.update-picture');

        // Teachers Routes
        Route::get('/teachers', [TeacherController::class, 'index'])->name('teachers.index');
        Route::get('/teachers/create', [TeacherController::class, 'create'])->name('teachers.create');
        Route::post('/teachers', [TeacherController::class, 'store'])->name('teachers.store');
        Route::get('/teachers/{teacher}', [TeacherController::class, 'show'])->name('teachers.show');
        Route::get('/teachers/{teacher}/edit', [TeacherController::class, 'edit'])->name('teachers.edit');
        Route::put('/teachers/{teacher}', [TeacherController::class, 'update'])->name('teachers.update');
        Route::delete('/teachers/{teacher}', [TeacherController::class, 'destroy'])->name('teachers.destroy');
        Route::put('/teachers/{teacher}/update-picture', [TeacherController::class, 'updateProfilePicture'])->name('teachers.update-picture');

        // Period Management Routes
        Route::get('/periods', [PeriodController::class, 'index'])->name('periods.index');
        Route::get('/periods/create', [PeriodController::class, 'create'])->name('periods.create');
        Route::post('/periods', [PeriodController::class, 'store'])->name('periods.store');
        Route::get('/periods/{period}/edit', [PeriodController::class, 'edit'])->name('periods.edit');
        Route::put('/periods/{period}', [PeriodController::class, 'update'])->name('periods.update');
        Route::delete('/periods/{period}', [PeriodController::class, 'destroy'])->name('periods.destroy');

        // Class Management Routes
        Route::get('/classes', [ClassController::class, 'index'])->name('classes.index');
        Route::get('/classes/create', [ClassController::class, 'create'])->name('classes.create');
        Route::post('/classes', [ClassController::class, 'store'])->name('classes.store');
        Route::get('/classes/{class}', [ClassController::class, 'show'])->name('classes.show');
        Route::get('/classes/{class}/edit', [ClassController::class, 'edit'])->name('classes.edit');
        Route::put('/classes/{class}', [ClassController::class, 'update'])->name('classes.update');
        Route::delete('/classes/{class}', [ClassController::class, 'destroy'])->name('classes.destroy');
        Route::get('/classes/{class}/manage-students', [ClassController::class, 'manageStudents'])->name('classes.manage-students');
        Route::put('/classes/{class}/update-students', [ClassController::class, 'updateStudents'])->name('classes.update-students');

        // Timetable Routes
        Route::get('/timetable', [TimetableController::class, 'index'])->name('timetable.index');
        Route::resource('timetable', TimetableController::class);
        Route::post('timetable/view', [TimetableController::class, 'viewTimetable'])->name('timetable.view');
        Route::get('timetable/class/{class_id}', [TimetableController::class, 'viewClassTimetable'])->name('timetable.class');

        // Messages Routes
        Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
        Route::get('/messages/compose', [MessageController::class, 'create'])->name('messages.compose');
        Route::get('/messages/inbox', [MessageController::class, 'inbox'])->name('messages.inbox');
        Route::get('/messages/sent', [MessageController::class, 'sent'])->name('messages.sent');
        Route::post('/messages/mark-all-read', [MessageController::class, 'markAllAsRead'])->name('messages.mark-all-read');
        Route::post('/messages', [MessageController::class, 'store'])->name('messages.store');
        Route::get('/messages/{message}', [MessageController::class, 'show'])->name('messages.show');
        Route::delete('/messages/{message}', [MessageController::class, 'destroy'])->name('messages.destroy');

        // Fees Routes
        Route::get('/fees', [FeeController::class, 'index'])->name('fees.index');
        Route::get('/fees-list', [FeeController::class, 'feesList'])->name('fees.list');
        Route::get('/fines-list', [FeeController::class, 'finesList'])->name('fines.list');

        // Fee Management
        Route::get('/fees/create', [FeeController::class, 'createFee'])->name('fees.create');
        Route::post('/fees', [FeeController::class, 'storeFee'])->name('fees.store');
        Route::get('/fees/{fee}', [FeeController::class, 'showFee'])->name('fees.show');
        Route::get('/fees/{fee}/edit', [FeeController::class, 'editFee'])->name('fees.edit');
        Route::put('/fees/{fee}', [FeeController::class, 'updateFee'])->name('fees.update');
        Route::delete('/fees/{fee}', [FeeController::class, 'destroyFee'])->name('fees.destroy');
        Route::post('/fees/{fee}/mark-paid', [FeeController::class, 'markFeePaid'])->name('fees.mark-paid');

        // Fine Management
        Route::get('/fines/create', [FeeController::class, 'createFine'])->name('fines.create');
        Route::post('/fines', [FeeController::class, 'storeFine'])->name('fines.store');
        Route::get('/fines/{fine}', [FeeController::class, 'showFine'])->name('fines.show');
        Route::get('/fines/{fine}/edit', [FeeController::class, 'editFine'])->name('fines.edit');
        Route::put('/fines/{fine}', [FeeController::class, 'updateFine'])->name('fines.update');
        Route::delete('/fines/{fine}', [FeeController::class, 'destroyFine'])->name('fines.destroy');
        Route::post('/fines/{fine}/mark-paid', [FeeController::class, 'markFinePaid'])->name('fines.mark-paid');
        Route::post('/fines/{fine}/mark-waived', [FeeController::class, 'markFineWaived'])->name('fines.mark-waived');

        // Student Fees
        Route::get('/student-fees/{student}', [FeeController::class, 'studentFees'])->name('student.fees');

        // Check Overdue Fees
        Route::post('/fees/check-overdue', [FeeController::class, 'checkOverdueFees'])->name('fees.check-overdue');

        // Fee Reports
        Route::get('/fees/report', [FeeController::class, 'report'])->name('fees.report');
        Route::post('/fees/report', [FeeController::class, 'report'])->name('fees.generate-report');
        Route::get('/fees/generate-report', [FeeController::class, 'generateReport'])->name('fees.generate-report-simple');

        // Public Report
        Route::get('/public-report', [FeeController::class, 'publicReport'])->name('fees.public-report');

        // Profile Routes
        Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
        Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::put('/profile/photo', [ProfileController::class, 'updatePhoto'])->name('profile.photo');

        // Activity Routes
        Route::get('/activities', [ActivityController::class, 'index'])->name('activities.index');
        Route::get('/activities/data', [ActivityController::class, 'getData'])->name('activities.data');
        Route::get('/activities/clear', [ActivityController::class, 'clearAll'])->name('activities.clear');
        Route::get('/activities/{id}', [ActivityController::class, 'show'])->name('activities.show');
    });
});

// Attendance routes
Route::prefix('dash/attendance')->middleware(['auth'])->group(function () {
    Route::get('/students', [AttendanceController::class, 'studentsIndex'])->name('attendance.students.index');
    Route::post('/students/mark', [AttendanceController::class, 'markStudentAttendance'])->name('attendance.students.mark');
    Route::get('/teachers', [AttendanceController::class, 'teachersIndex'])->name('attendance.teachers.index');
    Route::post('/teachers/mark', [AttendanceController::class, 'markTeacherAttendance'])->name('attendance.teachers.mark');
    Route::get('/reports', [AttendanceController::class, 'reports'])->name('attendance.reports');
    Route::post('/log-print', [AttendanceController::class, 'logPrintActivity'])->name('attendance.log-print');

    // New routes for editing and deleting attendance
    Route::get('/edit/{id}', [AttendanceController::class, 'edit'])->name('attendance.edit');
    Route::put('/update/{id}', [AttendanceController::class, 'update'])->name('attendance.update');
    Route::delete('/delete/{id}', [AttendanceController::class, 'destroy'])->name('attendance.delete');
});
