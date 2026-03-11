<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\VerifyPhoneRequest;
use App\Services\Auth\PhoneVerificationBroker;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PhoneVerificationController extends Controller
{
    public function store(Request $request, PhoneVerificationBroker $broker): RedirectResponse
    {
        $user = $request->user();

        if ($user->hasVerifiedPhone()) {
            return back()->with('phone_verification_message', 'Your account phone number is already verified.');
        }

        $debugCode = $broker->send($user);

        $response = back()->with('phone_verification_message', 'A phone verification code was issued. It expires in 10 minutes.');

        if ($debugCode !== null) {
            $response->with('phone_verification_debug_code', $debugCode);
        }

        return $response;
    }

    public function verify(VerifyPhoneRequest $request, PhoneVerificationBroker $broker): RedirectResponse
    {
        $broker->verify($request->user(), (string) $request->validated('code'));

        return back()->with('phone_verification_message', 'Your account phone number has been verified.');
    }
}
