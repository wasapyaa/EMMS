<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\AuthController;


Route::post('/attendance/scan', [AttendanceController::class, 'scan']);

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register/send-otp', [AuthController::class, 'sendRegisterOtp']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/forgot-password', [AuthController::class, 'sendOtp']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

Route::get('/attendance/history/{s_id}', [AttendanceController::class, 'history']);
Route::get('/events', [AttendanceController::class, 'events']);
Route::get('/participation/{s_id}', [AttendanceController::class, 'participation']);
Route::get('/profile/{s_id}', [AttendanceController::class, 'profile']);
Route::put('/profile/{s_id}', [AttendanceController::class, 'updateProfile']);
Route::post('/profile/{s_id}/password', [AttendanceController::class, 'updatePassword']);

Route::get('/student/ranking/{s_id}', [AttendanceController::class, 'ranking']);
Route::post('/student/join-semester', [AttendanceController::class, 'joinSemester']);

use App\Http\Controllers\Api\IdeaController;
Route::get('/ideas', [IdeaController::class, 'index']);
Route::post('/ideas', [IdeaController::class, 'store']);
Route::post('/ideas/{id}/vote', [IdeaController::class, 'vote']);
Route::post('/ideas/{id}/report', [IdeaController::class, 'report']);