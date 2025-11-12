<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\OtpService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function __construct(private readonly OtpService $otpService)
    {
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function loginWithEmail(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:6'],
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid Email Address',
            ], 404);
        }

        if (!Hash::check($credentials['password'], $user->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid credentials',
            ], 422);
        }

        Auth::login($user, $request->boolean('remember'));

        return response()->json([
            'status' => true,
            'redirect' => route('home'),
        ]);
    }

    public function loginWithAadhar(Request $request): JsonResponse
    {
        $data = $request->validate([
            'aadhar_number' => ['required', 'digits:12'],
        ]);

        $user = User::where('aadhar_number', $data['aadhar_number'])->first();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid Aadhar Number',
            ], 404);
        }

        $otp = $this->otpService->generateOtpFor($user);

        return response()->json([
            'status' => true,
            'message' => 'OTP sent to registered email address',
            'otp_token' => $otp->id,
        ]);
    }

    public function verifyOtp(Request $request): JsonResponse
    {
        $data = $request->validate([
            'otp_id' => ['required', 'integer', 'exists:otps,id'],
            'code' => ['required', 'digits:6'],
        ]);

        $verification = $this->otpService->verifyOtp($data['otp_id'], $data['code']);

        if (!$verification['status']) {
            return response()->json($verification, 422);
        }

        Auth::login($verification['user']);

        return response()->json([
            'status' => true,
            'message' => 'Login successful',
            'redirect' => route('home'),
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        Auth::guard()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'status' => true,
            'redirect' => route('login'),
        ]);
    }
}

