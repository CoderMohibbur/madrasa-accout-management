<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\OpenRegistrationRequest;
use App\Models\Donor;
use App\Models\Guardian;
use App\Models\Role;
use App\Models\User;
use App\Services\Auth\EmailVerificationNotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return $this->renderRegistrationView('public');
    }

    public function createDonor(): View
    {
        return $this->renderRegistrationView('donor');
    }

    public function createGuardian(): View
    {
        return $this->renderRegistrationView('guardian');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(OpenRegistrationRequest $request, EmailVerificationNotificationService $emailVerificationNotificationService): RedirectResponse
    {
        $intent = $request->registrationIntent();

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'email_verified_at' => null,
            'approval_status' => User::APPROVAL_NOT_REQUIRED,
            'account_status' => User::ACCOUNT_STATUS_ACTIVE,
            'phone' => $request->validated('phone'),
        ]);

        $this->ensureRegisteredUserRoleExists();
        $user->assignRole(User::ROLE_REGISTERED_USER);

        if ($intent === 'donor') {
            Donor::query()->create([
                'user_id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'portal_enabled' => false,
                'address' => null,
                'notes' => 'Open registration donor intent.',
                'isActived' => false,
                'isDeleted' => false,
            ]);
        }

        if ($intent === 'guardian') {
            Guardian::query()->create([
                'user_id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'address' => null,
                'notes' => 'Open registration guardian intent.',
                'portal_enabled' => false,
                'isActived' => true,
                'isDeleted' => false,
            ]);
        }

        Auth::login($user);
        $request->session()->regenerate();

        $emailVerificationSent = $emailVerificationNotificationService->send($user, 'open_registration');

        $redirect = redirect()
            ->route('registration.onboarding')
            ->with('status', $this->registrationSuccessMessage($intent, $emailVerificationSent));

        if (! $emailVerificationSent) {
            $redirect->with('email_verification_warning', 'Email verification is enabled for this account, but the current environment could not deliver the message automatically. Configure mail transport and use resend when ready.');
        }

        return $redirect;
    }

    public function onboarding(Request $request): View
    {
        $user = $request->user();

        abort_unless($user && $user->hasRole(User::ROLE_REGISTERED_USER), 403);

        $intent = 'public';

        if ($user->guardianProfile()->exists()) {
            $intent = 'guardian';
        } elseif ($user->donorProfile()->exists()) {
            $intent = 'donor';
        }

        return view('auth.onboarding', [
            'intent' => $intent,
            'user' => $user,
        ]);
    }

    private function renderRegistrationView(string $intent): View
    {
        return view('auth.register', [
            'registrationIntent' => $intent,
        ]);
    }

    private function ensureRegisteredUserRoleExists(): void
    {
        Role::query()->firstOrCreate(
            ['name' => User::ROLE_REGISTERED_USER],
            [
                'display_name' => 'Registered User',
                'description' => 'Self-registered account without portal eligibility.',
                'is_system' => true,
            ],
        );
    }

    private function registrationSuccessMessage(string $intent, bool $emailVerificationSent): string
    {
        return match ($intent) {
            'donor' => $emailVerificationSent
                ? 'Your account is ready, your donor interest has been recorded, and an email verification link has been sent. Donor portal access stays off until the later donor access steps are complete, and phone verification remains optional.'
                : 'Your account is ready, your donor interest has been recorded, and email verification is waiting on mail transport in this environment. Donor portal access stays off until the later donor access steps are complete, and phone verification remains optional.',
            'guardian' => $emailVerificationSent
                ? 'Your account is ready, your guardian onboarding has started, and an email verification link has been sent. Protected guardian access stays locked until later linkage and authorization steps are complete, and phone verification remains optional.'
                : 'Your account is ready, your guardian onboarding has started, and email verification is waiting on mail transport in this environment. Protected guardian access stays locked until later linkage and authorization steps are complete, and phone verification remains optional.',
            default => $emailVerificationSent
                ? 'Your account is ready. Continue from the neutral onboarding space, verify your email for legacy verified routes, and add phone verification later only if you want that extra contact channel.'
                : 'Your account is ready. Continue from the neutral onboarding space, and add phone verification later only if you want that extra contact channel while email delivery stays pending on the current environment.',
        };
    }
}
