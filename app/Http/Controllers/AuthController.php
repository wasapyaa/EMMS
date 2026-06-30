<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrganizerOtpMail;
use Carbon\Carbon;

// 🔥 TAMBAH INI
use App\Models\Student;
use App\Models\Organizer;
use App\Models\Admin;

class AuthController extends Controller
{
   public function login(Request $request)
{
    $request->validate([
        'role' => 'required',
        'email' => 'required|email',
        'password' => 'required'
    ]);

    if ($request->role === 'student') {
        $user = Student::where('email', $request->email)->first();
        if ($user && Hash::check($request->password, $user->pass_hash)) {
            session(['role' => 'student', 'user_id' => $user->s_id]);
            return redirect('/student/dashboard');
        }
    }

    if ($request->role === 'organizer') {

        $organizer = Organizer::where('email', $request->email)->first();

        if ($organizer && Hash::check($request->password, $organizer->pass_hash)) {

            session([
                'role' => 'organizer',
                'organizer_id' => $organizer->o_id   // ikut PK kau
            ]);

            return redirect('/organizer/dashboard');
        }

        return back()->withErrors([
            'login' => 'Invalid organizer credentials'
        ]);
    }

    if ($request->role === 'admin') {
        $user = Admin::where('email', $request->email)->first();
        if ($user && Hash::check($request->password, $user->pass_hash)) {
            session(['role' => 'admin', 'admin_id' => $user->a_id]);
            return redirect('/admin/dashboard');
        }
    }

    return back()->withErrors([
        'login' => 'Invalid credentials for selected role'
    ]);
}

public function showLogin()
{
    return view('auth.login');
}

public function logout(Request $request)
{
    $role = session('role');

    if ($role === 'organizer') {
        session()->forget(['organizer_id', 'role']);
    } elseif ($role === 'admin') {
        session()->forget(['admin_id', 'role']);
    } elseif ($role === 'student') {
        session()->forget(['user_id', 'role']);
    } else {
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }

    return redirect('/login');
}

public function showStudentRegister()
    {
        return view('auth.student-register');
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

    // Handle signup
    public function studentRegister(Request $request)
    {
        // Validation
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => [
                'required',
                'email',
                'unique:students,email',
                'regex:/^[0-9]+@student\.uitm\.edu\.my$/'
            ],
            'phone' => 'required|digits_between:10,11',
            'password' => 'required|min:8|confirmed',
            'otp_code' => 'required|string|size:6'
        ], [
            'email.regex' => 'Please use a valid UiTM student email (e.g. 2021123456@student.uitm.edu.my).',
            'otp_code.required' => 'The OTP code is required.',
            'otp_code.size' => 'The OTP code must be 6 digits.'
        ]);

        // Verify OTP
        $cachedOtp = \Illuminate\Support\Facades\Cache::get('register_otp_' . $request->email);
        if (!$cachedOtp || $cachedOtp !== $request->otp_code) {
            return back()->withInput()->withErrors([
                'otp_code' => 'Invalid or expired OTP code. Please request a new one.'
            ]);
        }

        // Extract matric number from email
        $num_matrics = explode('@', $request->email)[0];

        // Create student
        Student::create([
            'num_matrics' => $num_matrics,
            'name'        => $request->name,
            'email'       => $request->email,
            'phone'       => $request->phone,
            'pass_hash'   => Hash::make($request->password),
            'total_merit' => 0
        ]);

        // Clear OTP Cache
        \Illuminate\Support\Facades\Cache::forget('register_otp_' . $request->email);

        return redirect('/login')->with('success', 'Account created successfully. Please login.');
    }

   // Show organizer signup page
public function showOrganizerRegister()
{
    return view('auth.organizer-register');
}

// Handle organizer signup
public function organizerRegister(Request $request)
{
    $request->validate([
        'club_name' => 'required|string|max:150',
        'pic_name'          => 'required|string|max:100',
        'email'             => 'required|email|unique:organizers,email',
        'phone'             => 'required|digits_between:10,11',
        'password'          => 'required|min:8|confirmed'
    ]);

    Organizer::create([
        'club_name' => $request->club_name,
        'pic_name'          => $request->pic_name,
        'email'             => $request->email,
        'phone'             => $request->phone,
        'pass_hash'         => Hash::make($request->password),
        'status'            => 'pending' // tunggu HEP approve (best practice)
    ]);

    return redirect('/login')
        ->with('success', 'Organizer account created. Please wait for admin approval.');
}

    public function showOrganizerForgotPassword()
    {
        return view('auth.organizer-forgot-password');
    }

    public function sendOrganizerOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $organizer = Organizer::where('email', $request->email)->first();

        if (!$organizer) {
            return back()->with('error', 'We could not find an organizer account with that email address.');
        }

        // Generate 6-digit OTP
        $otp = sprintf('%06d', mt_rand(0, 999999));
        
        $organizer->update([
            'otp_code' => $otp,
            'otp_expires_at' => Carbon::now()->addMinutes(15)
        ]);

        try {
            Mail::to($organizer->email)->send(new OrganizerOtpMail($otp, $organizer->pic_name));
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to send OTP email. Please verify SMTP settings: ' . $e->getMessage());
        }

        return redirect()->route('organizer.reset-password.show', ['email' => $organizer->email])
            ->with('status', 'An OTP code has been sent to your email.');
    }

    public function showOrganizerResetPassword(Request $request)
    {
        $email = $request->query('email');
        
        if (!$email) {
            return redirect()->route('organizer.forgot-password.show');
        }

        return view('auth.organizer-reset-password', compact('email'));
    }

    public function organizerResetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp_code' => 'required|string|size:6',
            'password' => 'required|min:8|confirmed'
        ]);

        $organizer = Organizer::where('email', $request->email)->first();

        if (!$organizer) {
            return back()->with('error', 'Invalid request.');
        }

        if (!$organizer->otp_code || !$organizer->otp_expires_at || Carbon::parse($organizer->otp_expires_at)->isPast()) {
            return back()->with('error', 'OTP has expired. Please request a new code.');
        }

        if ($organizer->otp_code !== $request->otp_code) {
            return back()->withInput()->with('error', 'Invalid OTP code. Please check and try again.');
        }

        // OTP is valid, update password and clear OTP fields
        $organizer->update([
            'pass_hash' => Hash::make($request->password),
            'otp_code' => null,
            'otp_expires_at' => null
        ]);

        return redirect('/login')->with('success', 'Your password has been successfully reset. You can now log in.');
    }

}

