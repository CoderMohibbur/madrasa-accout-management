<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Services\Auth\ContactVerificationAuditLogger;
use App\Services\Auth\EmailVerificationNotificationService;
use App\Services\Auth\PhoneVerificationBroker;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(
        ProfileUpdateRequest $request,
        PhoneVerificationBroker $phoneVerificationBroker,
        ContactVerificationAuditLogger $auditLogger,
        EmailVerificationNotificationService $emailVerificationNotificationService,
    ): RedirectResponse
    {
        $user = $request->user();
        $before = [
            'email' => $user->email,
            'phone' => $user->maskedPhone(),
            'email_verified_at' => $user->email_verified_at?->toIso8601String(),
            'phone_verified_at' => $user->phone_verified_at?->toIso8601String(),
        ];

        $user->fill($request->validated());

        $emailChanged = $user->isDirty('email');
        $phoneChanged = $user->isDirty('phone');

        if ($emailChanged) {
            $user->email_verified_at = null;
        }

        if ($phoneChanged) {
            $user->phone_verified_at = null;
            $phoneVerificationBroker->invalidate($user);
        }

        $user->save();

        if ($emailChanged || $phoneChanged) {
            $auditLogger->record(
                actor: $user,
                target: $user,
                event: 'verification.contact.channels_changed',
                summary: 'Profile contact channels changed and verification state was reset for the affected fields.',
                before: $before,
                after: [
                    'email' => $user->email,
                    'phone' => $user->maskedPhone(),
                    'email_verified_at' => $user->email_verified_at?->toIso8601String(),
                    'phone_verified_at' => $user->phone_verified_at?->toIso8601String(),
                ],
                context: [
                    'changed_channels' => array_values(array_filter([
                        $emailChanged ? 'email' : null,
                        $phoneChanged ? 'phone' : null,
                    ])),
                ],
            );
        }

        $emailVerificationSent = null;

        if ($emailChanged && $user instanceof MustVerifyEmail) {
            $emailVerificationSent = $emailVerificationNotificationService->send($user, 'profile_email_change');
        }

        $redirect = Redirect::route('profile.edit')->with('status', 'profile-updated');

        if ($emailChanged || $phoneChanged) {
            $redirect->with('contact_verification_message', 'Verification was reset only for the contact channels you changed.');
        }

        if ($emailChanged) {
            $redirect->with(
                ($emailVerificationSent ?? false) ? 'email_verification_message' : 'email_verification_warning',
                ($emailVerificationSent ?? false)
                    ? 'A verification link was sent to your updated email address.'
                    : 'Your email changed, but this environment could not deliver the verification message automatically. Configure mail transport and resend when ready.',
            );
        }

        return $redirect;
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
