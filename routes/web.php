<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\OrganizerController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AttendanceController;
use Illuminate\Support\Facades\Mail;



Route::get('/', function () {
    return view('welcome');
});

/* LOGIN */
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::match(['get', 'post'], '/logout', [AuthController::class, 'logout'])->name('logout');

Route::prefix('student')->group(function () {
    Route::get('/dashboard', [StudentController::class, 'dashboard']);
    Route::post('/join-semester', [StudentController::class, 'joinSemester']);
    Route::get('/events', [StudentController::class, 'events']);
    Route::get('/participation', [StudentController::class, 'participation']);
    Route::get('/ranking', [StudentController::class, 'ranking']);
    Route::get('/profile', [StudentController::class, 'profile']);
Route::post('/profile/update', [StudentController::class, 'updateProfile']);
Route::post('/profile/password', [StudentController::class, 'updatePassword']);
});

Route::get('/student/ranking-history', [StudentController::class, 'rankingHistory']);

Route::post('/student/logout', [AuthController::class, 'logout'])
    ->name('student.logout');

Route::post('/organizer/logout', [AuthController::class, 'logout'])
    ->name('organizer.logout');

    // Student Sign Up
Route::get('/student/register', [AuthController::class, 'showStudentRegister']);
Route::post('/student/register', [AuthController::class, 'studentRegister']);
Route::post('/student/register/send-otp', [AuthController::class, 'sendRegisterOtp'])->name('student.register.send-otp');


// Organizer signup
Route::get('/organizer/register', [AuthController::class, 'showOrganizerRegister']);
Route::post('/organizer/register', [AuthController::class, 'organizerRegister']);

// Organizer forgot password (OTP via SMTP)
Route::get('/organizer/forgot-password', [AuthController::class, 'showOrganizerForgotPassword'])->name('organizer.forgot-password.show');
Route::post('/organizer/forgot-password', [AuthController::class, 'sendOrganizerOtp'])->name('organizer.forgot-password.send');
Route::get('/organizer/reset-password', [AuthController::class, 'showOrganizerResetPassword'])->name('organizer.reset-password.show');
Route::post('/organizer/reset-password', [AuthController::class, 'organizerResetPassword'])->name('organizer.reset-password.update');





Route::get('/organizer/dashboard', [OrganizerController::class, 'dashboard']);
Route::get('/organizer/proposals', [OrganizerController::class, 'proposalList']);
Route::get('/organizer/proposals/create', [OrganizerController::class, 'createProposal']);
Route::get('/organizer/proposals/{id}', [OrganizerController::class, 'showProposal']);
Route::post('/organizer/proposals', [OrganizerController::class, 'storeProposal']);
Route::get('/organizer/events/approved', [OrganizerController::class, 'approvedEvents']);
Route::get('/organizer/events/{id}/qr', [OrganizerController::class, 'downloadQr']);
Route::get('/organizer/events/{id}', [OrganizerController::class, 'showEvent']);
Route::get('/organizer/ideas', [OrganizerController::class, 'ideas']);
Route::get('/organizer/profile', [OrganizerController::class, 'profile']);
Route::post('/organizer/profile/update', [OrganizerController::class, 'updateProfile']);
Route::post('/organizer/profile/password', [OrganizerController::class, 'updatePassword']);

Route::get('/organizer/proposals/{id}/edit', [OrganizerController::class, 'editProposal']);
Route::post('/organizer/proposals/{id}/update', [OrganizerController::class, 'updateProposal']);
Route::delete('/organizer/proposals/{id}', [OrganizerController::class, 'deleteProposal']);



Route::prefix('admin')->group(function () {

    Route::get('/dashboard', [AdminController::class, 'dashboard']);
    Route::get('/hostel', [AdminController::class, 'hostel']);
    Route::post('/hostel-eligibility', [AdminController::class, 'updateHostelEligibility']);
    Route::get('/profile', [AdminController::class, 'profile']);
    Route::post('/profile', [AdminController::class, 'updateProfile']);
    Route::get('/merit', [AdminController::class, 'viewMerit']);
    Route::get('/merit/export', [AdminController::class, 'exportMerit']);
    Route::get('/merit/{id}', [AdminController::class, 'viewStudentMerit']);
    Route::get('/organizers', [AdminController::class, 'organizers']);
    Route::get('/events', [AdminController::class, 'events']);
    Route::get('/ideas', [AdminController::class, 'ideas']);
    Route::get('/reset', [AdminController::class, 'reset']);
    Route::post('/reset', [AdminController::class, 'processReset']);

});

Route::get('/admin/send-reminder', [AdminController::class, 'showSendReminder']);
Route::post('/admin/send-reminder', [AdminController::class, 'sendReminderToAll']);

Route::get('/admin/organizers', [AdminController::class, 'manageOrganizers']);
Route::post('/admin/organizers/{id}/approve', [AdminController::class, 'approveOrganizer']);
Route::post('/admin/organizers/{id}/reject', [AdminController::class, 'rejectOrganizer']);
Route::get('/admin/organizers/{id}', [AdminController::class, 'viewOrganizer']);

Route::get('/admin/events', [AdminController::class, 'manageEvents']);
Route::post('/admin/events/{id}/approve', [AdminController::class, 'approveEvent']);
Route::post('/admin/events/{id}/reject', [AdminController::class, 'rejectEvent']);
Route::get('/admin/events/{id}/edit', [AdminController::class, 'editEvent']);
Route::post('/admin/events/{id}/edit', [AdminController::class, 'updateEvent']);
Route::get('/admin/events/{id}/qr', [AdminController::class, 'downloadQr']);
Route::get('/admin/events/{id}', [AdminController::class, 'viewEvent']);

Route::get('/attendance/{token}', [AttendanceController::class, 'show']);
Route::post('/attendance/{token}', [AttendanceController::class, 'confirm']);






