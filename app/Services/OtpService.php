<?php

namespace App\Services;

use App\Models\Otp;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class OtpService
{
    public function generateOtpFor(User $user): Otp
    {
        return DB::transaction(function () use ($user) {
            $code = random_int(100000, 999999);

            $otp = Otp::create([
                'user_id' => $user->id,
                'code' => $code,
                'expires_at' => Carbon::now()->addMinutes((int) config('otp.expiry', config('services.otp.expiry', env('OTP_EXPIRY_MINUTES', 10)))),
            ]);

            $this->sendOtpMail($user, $code);

            return $otp;
        });
    }

    public function verifyOtp(int $otpId, string $code): array
    {
        $otp = Otp::find($otpId);

        if (!$otp) {
            return [
                'status' => false,
                'message' => 'Invalid OTP request.',
            ];
        }

        if ($otp->verified_at) {
            return [
                'status' => false,
                'message' => 'OTP already used.',
            ];
        }

        if ($otp->expires_at->isPast()) {
            return [
                'status' => false,
                'message' => 'OTP expired.',
            ];
        }

        if (!hash_equals($otp->code, $code)) {
            return [
                'status' => false,
                'message' => 'Incorrect OTP.',
            ];
        }

        $otp->update([
            'verified_at' => Carbon::now(),
        ]);

        return [
            'status' => true,
            'user' => $otp->user,
        ];
    }

    protected function sendOtpMail(User $user, string $code): void
    {
        $subject = 'Your RDFFinance OTP';
        $body = "Dear {$user->name},\n\nYour OTP for RDFFinance login is {$code}.\nThis code expires in " .
            env('OTP_EXPIRY_MINUTES', 10) . " minutes.\n\nRegards,\nRDFFinance Team";

        Mail::raw($body, function ($message) use ($user, $subject) {
            $message->to($user->email)
                ->subject($subject);
        });
    }
}

