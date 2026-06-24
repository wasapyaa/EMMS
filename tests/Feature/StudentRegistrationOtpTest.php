<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use App\Mail\StudentRegisterOtpMail;
use App\Models\Student;

class StudentRegistrationOtpTest extends TestCase
{
    use RefreshDatabase;

    public function test_web_cannot_send_otp_with_invalid_email()
    {
        Mail::fake();

        $response = $this->postJson('/student/register/send-otp', [
            'email' => 'invalid-email@example.com',
            'name' => 'John Doe'
        ]);

        $response->assertStatus(422);
        Mail::assertNothingSent();
    }

    public function test_web_can_send_otp_and_register_successfully()
    {
        Mail::fake();
        Cache::flush();

        // 1. Send OTP
        $response = $this->postJson('/student/register/send-otp', [
            'email' => '2021123456@student.uitm.edu.my',
            'name' => 'John Doe'
        ]);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'success']);

        // Check OTP was stored in cache
        $cachedOtp = Cache::get('register_otp_2021123456@student.uitm.edu.my');
        $this->assertNotNull($cachedOtp);

        Mail::assertSent(StudentRegisterOtpMail::class, function ($mail) use ($cachedOtp) {
            return $mail->otp === $cachedOtp && $mail->studentName === 'John Doe';
        });

        // 2. Register with invalid OTP
        $response = $this->post('/student/register', [
            'name' => 'John Doe',
            'email' => '2021123456@student.uitm.edu.my',
            'phone' => '0123456789',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'otp_code' => '000000'
        ]);

        $response->assertSessionHasErrors('otp_code');
        $this->assertDatabaseMissing('students', ['email' => '2021123456@student.uitm.edu.my']);

        // 3. Register with valid OTP
        $response = $this->post('/student/register', [
            'name' => 'John Doe',
            'email' => '2021123456@student.uitm.edu.my',
            'phone' => '0123456789',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'otp_code' => $cachedOtp
        ]);

        $response->assertRedirect('/login');
        $this->assertDatabaseHas('students', [
            'email' => '2021123456@student.uitm.edu.my',
            'name' => 'John Doe'
        ]);

        // Check OTP cache was cleared
        $this->assertNull(Cache::get('register_otp_2021123456@student.uitm.edu.my'));
    }

    public function test_api_can_send_otp_and_register_successfully()
    {
        Mail::fake();
        Cache::flush();

        // 1. Send OTP
        $response = $this->postJson('/api/register/send-otp', [
            'email' => '2021999999@student.uitm.edu.my',
            'name' => 'Jane Doe'
        ]);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'success']);

        $cachedOtp = Cache::get('register_otp_2021999999@student.uitm.edu.my');
        $this->assertNotNull($cachedOtp);

        Mail::assertSent(StudentRegisterOtpMail::class, function ($mail) use ($cachedOtp) {
            return $mail->otp === $cachedOtp && $mail->studentName === 'Jane Doe';
        });

        // 2. Register via API with invalid OTP
        $response = $this->postJson('/api/register', [
            'name' => 'Jane Doe',
            'email' => '2021999999@student.uitm.edu.my',
            'phone' => '0123456789',
            'password' => 'password123',
            'otp_code' => '000000'
        ]);

        $response->assertStatus(400);
        $response->assertJson(['status' => 'fail']);
        $this->assertDatabaseMissing('students', ['email' => '2021999999@student.uitm.edu.my']);

        // 3. Register via API with valid OTP
        $response = $this->postJson('/api/register', [
            'name' => 'Jane Doe',
            'email' => '2021999999@student.uitm.edu.my',
            'phone' => '0123456789',
            'password' => 'password123',
            'otp_code' => $cachedOtp
        ]);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'success']);
        $this->assertDatabaseHas('students', [
            'email' => '2021999999@student.uitm.edu.my',
            'name' => 'Jane Doe'
        ]);

        // Check OTP cache was cleared
        $this->assertNull(Cache::get('register_otp_2021999999@student.uitm.edu.my'));
    }
}
