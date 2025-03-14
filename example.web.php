<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\DashboardController;
use App\Http\Controllers\Backend\Auth\ResetPasswordController;
use App\Http\Controllers\Backend\Auth\LoginController;
use App\Http\Controllers\Backend\Auth\RegisterController;
use App\Http\Controllers\Backend\Auth\ForgotPasswordController;
use App\Http\Controllers\Backend\ParentController;
use App\Http\Controllers\Backend\TeacherController;
use App\Http\Controllers\Backend\PeriodController;
use App\Http\Controllers\Backend\ClassController;
use App\Http\Controllers\Backend\TimetableController;
use App\Http\Controllers\Backend\MessageController;
use App\Http\Controllers\Backend\FeeController;
use App\Http\Controllers\Backend\ProfileController;
use App\Http\Controllers\Backend\StudentController;



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
    return redirect('/admin/login');
});
Route::get('/admin', function () {
    return redirect('/admin/login');
});

Route::prefix('admin')->group(function () {
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

        // Class Management Routes
        Route::get('/classes', [ClassController::class, 'index'])->name('classes.index');

        // Timetable Routes
        Route::get('/timetable', [TimetableController::class, 'index'])->name('timetable.index');

        // Messages Routes
        Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');

        // Fees Routes
        Route::get('/fees', [FeeController::class, 'index'])->name('fees.index');

        // Profile Routes
        Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
        Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::put('/profile/photo', [ProfileController::class, 'updatePhoto'])->name('profile.photo');
    });
});
