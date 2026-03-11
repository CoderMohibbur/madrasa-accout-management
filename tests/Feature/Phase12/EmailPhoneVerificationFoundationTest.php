<?php

namespace Tests\Feature\Phase12;

use App\Models\Role;
use App\Models\User;
use App\Services\Auth\PhoneVerificationBroker;
use Illuminate\Auth\Notifications\VerifyEmail as VerifyEmailNotification;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class EmailPhoneVerificationFoundationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(ValidateCsrfToken::class);
        Cache::flush();

        Role::query()->create(['name' => User::ROLE_REGISTERED_USER, 'display_name' => 'Registered User']);
    }

    public function test_registration_can_capture_an_optional_phone_and_issue_the_email_verification_notification(): void
    {
        Notification::fake();

        $response = $this->post('/register', [
            'name' => 'Public User',
            'email' => 'PUBLIC@EXAMPLE.COM',
            'phone' => '01700 000000',
            'password' => 'password',
            'password_confirmation' => 'password',
            'intent' => 'public',
        ]);

        $user = User::query()->where('email', 'public@example.com')->firstOrFail();

        $response->assertRedirect(route('registration.onboarding', absolute: false));
        $this->assertAuthenticatedAs($user);
        $this->assertSame('+8801700000000', $user->phone);
        $this->assertNull($user->phone_verified_at);
        $this->assertNull($user->email_verified_at);
        $this->assertTrue($user->hasRole(User::ROLE_REGISTERED_USER));
        Notification::assertSentTo($user, VerifyEmailNotification::class);
        $this->assertDatabaseHas('audit_logs', [
            'auditable_type' => User::class,
            'auditable_id' => $user->id,
            'event' => 'verification.email.send_requested',
        ]);
    }

    public function test_email_resend_uses_the_approved_cooldown_and_hourly_cap_without_touching_phone_state(): void
    {
        Notification::fake();

        $user = $this->makeRegisteredUser([
            'phone' => '01740000000',
        ]);

        $this->actingAs($user)
            ->from('/registration/onboarding')
            ->post(route('verification.send'))
            ->assertSessionHas('email_verification_message');

        $this->actingAs($user)
            ->from('/registration/onboarding')
            ->post(route('verification.send'))
            ->assertSessionHasErrors(['email_verification']);

        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->travel(61)->seconds();

            $this->actingAs($user)
                ->from('/registration/onboarding')
                ->post(route('verification.send'))
                ->assertSessionHas('email_verification_message');
        }

        $this->travel(61)->seconds();

        $this->actingAs($user)
            ->from('/registration/onboarding')
            ->post(route('verification.send'))
            ->assertSessionHasErrors(['email_verification']);

        $this->assertNull($user->fresh()->phone_verified_at);
        $this->assertDatabaseHas('audit_logs', [
            'auditable_type' => User::class,
            'auditable_id' => $user->id,
            'event' => 'verification.email.send_limit_reached',
        ]);

        $this->travelBack();
    }

    public function test_phone_verification_can_complete_without_granting_email_verification_or_portal_access(): void
    {
        $user = $this->makeRegisteredUser([
            'phone' => '01710000000',
        ]);

        $this->actingAs($user)
            ->from('/registration/onboarding')
            ->post(route('verification.phone.send'))
            ->assertSessionHas('phone_verification_message');

        $code = app(PhoneVerificationBroker::class)->currentTestingCode($user->fresh());

        $this->assertNotNull($code);

        $this->actingAs($user)
            ->from('/registration/onboarding')
            ->post(route('verification.phone.verify'), [
                'code' => $code,
            ])
            ->assertSessionHas('phone_verification_message');

        $user->refresh();

        $this->assertNotNull($user->phone_verified_at);
        $this->assertNull($user->email_verified_at);
        $this->assertTrue($user->hasRole(User::ROLE_REGISTERED_USER));
        $this->get('/dashboard')->assertRedirect(route('verification.notice', absolute: false));
        $this->assertDatabaseHas('audit_logs', [
            'auditable_type' => User::class,
            'auditable_id' => $user->id,
            'event' => 'verification.phone.verified',
        ]);
    }

    public function test_phone_verification_fails_closed_when_the_number_is_already_verified_on_another_active_account(): void
    {
        User::factory()->create([
            'phone' => '+8801720000000',
            'phone_verified_at' => now(),
            'approval_status' => User::APPROVAL_NOT_REQUIRED,
            'account_status' => User::ACCOUNT_STATUS_ACTIVE,
        ]);

        $user = $this->makeRegisteredUser([
            'phone' => '01720000000',
        ]);

        $this->actingAs($user)
            ->from('/registration/onboarding')
            ->post(route('verification.phone.send'))
            ->assertSessionHas('phone_verification_message');

        $code = app(PhoneVerificationBroker::class)->currentTestingCode($user->fresh());

        $this->actingAs($user)
            ->from('/registration/onboarding')
            ->post(route('verification.phone.verify'), [
                'code' => $code,
            ])
            ->assertSessionHasErrors(['phone_verification']);

        $this->assertNull($user->fresh()->phone_verified_at);
        $this->assertDatabaseHas('audit_logs', [
            'auditable_type' => User::class,
            'auditable_id' => $user->id,
            'event' => 'verification.phone.verify_conflict',
        ]);
    }

    public function test_phone_verification_applies_a_temporary_cooldown_after_repeated_invalid_codes(): void
    {
        $user = $this->makeRegisteredUser([
            'phone' => '01730000000',
        ]);

        $this->actingAs($user)
            ->from('/registration/onboarding')
            ->post(route('verification.phone.send'))
            ->assertSessionHas('phone_verification_message');

        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->actingAs($user)
                ->from('/registration/onboarding')
                ->post(route('verification.phone.verify'), [
                    'code' => '000000',
                ])
                ->assertSessionHasErrors(['phone_verification_code']);
        }

        $this->actingAs($user)
            ->from('/registration/onboarding')
            ->post(route('verification.phone.verify'), [
                'code' => '000000',
            ])
            ->assertSessionHasErrors(['phone_verification']);

        $this->assertDatabaseHas('audit_logs', [
            'auditable_type' => User::class,
            'auditable_id' => $user->id,
            'event' => 'verification.phone.cooldown_applied',
        ]);
    }

    public function test_profile_contact_changes_reset_only_the_changed_verification_axis(): void
    {
        Notification::fake();

        $user = $this->makeRegisteredUser([
            'email_verified_at' => now(),
            'phone' => '+8801750000000',
            'phone_verified_at' => now(),
        ]);

        $this->actingAs($user)
            ->patch(route('profile.update'), [
                'name' => $user->name,
                'email' => $user->email,
                'phone' => '+8801800000000',
            ])
            ->assertRedirect(route('profile.edit', absolute: false));

        $user->refresh();
        $this->assertNotNull($user->email_verified_at);
        $this->assertNull($user->phone_verified_at);

        $user->forceFill([
            'phone_verified_at' => now(),
        ])->save();

        $this->actingAs($user)
            ->patch(route('profile.update'), [
                'name' => $user->name,
                'email' => 'updated@example.com',
                'phone' => $user->phone,
            ])
            ->assertRedirect(route('profile.edit', absolute: false));

        $user->refresh();
        $this->assertNull($user->email_verified_at);
        $this->assertNotNull($user->phone_verified_at);
        Notification::assertSentTo($user, VerifyEmailNotification::class);
        $this->assertDatabaseHas('audit_logs', [
            'auditable_type' => User::class,
            'auditable_id' => $user->id,
            'event' => 'verification.contact.channels_changed',
        ]);
    }

    private function makeRegisteredUser(array $overrides = []): User
    {
        $user = User::factory()
            ->unverified()
            ->create(array_merge([
                'approval_status' => User::APPROVAL_NOT_REQUIRED,
                'account_status' => User::ACCOUNT_STATUS_ACTIVE,
            ], $overrides));

        $user->assignRole(User::ROLE_REGISTERED_USER);

        return $user;
    }
}
