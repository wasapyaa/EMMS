<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\StudentOtpMail;
use Carbon\Carbon;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required'
        ]);

        $student = Student::where('email', $request->email)->first();

        if (!$student || !Hash::check($request->password, $student->pass_hash)) {
            return response()->json([
                'status'  => 'fail',
                'message' => 'Invalid email or password'
            ], 401);
        }

        return response()->json([
            'status' => 'success',
            's_id' => $student->s_id,
            'name' => $student->name,
            'num_matrics' => $student->num_matrics,
        ]);
    }

    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $student = Student::where('email', $request->email)->first();

        if (!$student) {
            return response()->json([
                'status' => 'fail',
                'message' => 'No student account found with this email address.'
            ], 404);
        }

        // Generate 6-digit OTP
        $otp = sprintf('%06d', mt_rand(0, 999999));

        $student->update([
            'otp_code' => $otp,
            'otp_expires_at' => Carbon::now()->addMinutes(15)
        ]);

        try {
            Mail::to($student->email)->send(new StudentOtpMail($otp, $student->name));
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Failed to send OTP email. Please verify SMTP settings: ' . $e->getMessage()
            ], 500);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'An OTP code has been sent to your email.'
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp_code' => 'required|string|size:6',
            'password' => 'required|min:8|confirmed'
        ]);

        $student = Student::where('email', $request->email)->first();

        if (!$student) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Invalid request.'
            ], 400);
        }

        if (!$student->otp_code || !$student->otp_expires_at || Carbon::parse($student->otp_expires_at)->isPast()) {
            return response()->json([
                'status' => 'fail',
                'message' => 'OTP has expired. Please request a new code.'
            ], 400);
        }

        if ($student->otp_code !== $request->otp_code) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Invalid OTP code. Please check and try again.'
            ], 400);
        }

        // OTP is valid, update password and clear OTP fields
        $student->update([
            'pass_hash' => Hash::make($request->password),
            'otp_code' => null,
            'otp_expires_at' => null
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Your password has been successfully reset. You can now log in.'
        ]);
    }

    public function sendRegisterOtp(Request $request)
    {
        $request->validate([
            'email' => [
                'required',
                'email',
                'unique:students,email',
                'regex:/^[0-9]+@student\.uitm\.edu\.my$/'
            ]
        ], [
            'email.regex' => 'Please use a valid UiTM student email (e.g. 2021123456@student.uitm.edu.my).',
            'email.unique' => 'This email address is already registered.'
        ]);

        $otp = sprintf('%06d', mt_rand(0, 999999));
        
        \Illuminate\Support\Facades\Cache::put('register_otp_' . $request->email, $otp, now()->addMinutes(15));

        $studentName = $request->input('name') ?: 'UiTM Student';

        try {
            Mail::to($request->email)->send(new \App\Mail\StudentRegisterOtpMail($otp, $studentName));
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Failed to send OTP email. Please verify SMTP settings: ' . $e->getMessage()
            ], 500);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'An OTP code has been sent to your email.'
        ]);
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => [
                'required',
                'email',
                'unique:students,email',
                'regex:/^[0-9]+@student\.uitm\.edu\.my$/'
            ],
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:6',
            'otp_code' => 'required|string|size:6'
        ], [
            'email.regex' => 'Please use a valid UiTM student email.',
            'email.unique' => 'This email address is already registered.',
            'otp_code.required' => 'OTP code is required.',
            'otp_code.size' => 'OTP code must be 6 digits.'
        ]);

        // Verify OTP
        $cachedOtp = \Illuminate\Support\Facades\Cache::get('register_otp_' . $request->email);
        if (!$cachedOtp || $cachedOtp !== $request->otp_code) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Invalid or expired OTP code. Please request a new one.'
            ], 400);
        }

        // Extract matric number from email
        $num_matrics = explode('@', $request->email)[0];

        // Create student
        $student = Student::create([
            'num_matrics' => $num_matrics,
            'name'        => $request->name,
            'email'       => $request->email,
            'phone'       => $request->phone,
            'pass_hash'   => Hash::make($request->password),
            'total_merit' => 0
        ]);

        // Clear OTP Cache
        \Illuminate\Support\Facades\Cache::forget('register_otp_' . $request->email);

        return response()->json([
            'status' => 'success',
            'message' => 'Registration successful! You can now log in.',
            'student' => [
                's_id' => $student->s_id,
                'name' => $student->name,
                'email' => $student->email,
            ]
        ]);
    }
}
