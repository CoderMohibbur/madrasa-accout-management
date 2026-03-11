<?php

namespace Tests\Feature\Phase12;

use App\Models\Guardian;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExternalAdmissionUrlImplementationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::query()->create([
            'name' => User::ROLE_REGISTERED_USER,
            'display_name' => 'Registered User',
        ]);
        Role::query()->create([
            'name' => 'guardian',
            'display_name' => 'Guardian',
        ]);
        Role::query()->create([
            'name' => 'donor',
            'display_name' => 'Donor',
        ]);
        Role::query()->create([
            'name' => 'management',
            'display_name' => 'Management',
        ]);

        config([
            'app.url' => 'https://example.test',
            'portal.admission.external_url' => 'https://attawheedic.com/admission/',
        ]);
    }

    public function test_public_surfaces_route_admission_through_the_internal_information_page_and_shared_external_cta(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('href="' . route('admission') . '"', false)
            ->assertDontSee('href="https://attawheedic.com/admission/"', false);

        $this->get('/donate')
            ->assertOk()
            ->assertSee('href="' . route('admission') . '"', false)
            ->assertDontSee('href="https://attawheedic.com/admission/"', false);

        $this->get('/admission')
            ->assertOk()
            ->assertSeeText('Admission information')
            ->assertSeeText('Open external application')
            ->assertSee('href="https://attawheedic.com/admission/"', false);
    }

    public function test_missing_external_admission_config_keeps_public_and_guardian_information_visible_without_a_live_cta(): void
    {
        config([
            'portal.admission.external_url' => '   ',
        ]);

        $this->get('/admission')
            ->assertOk()
            ->assertSeeText('Admission information')
            ->assertSeeText('External application link unavailable')
            ->assertDontSeeText('Open external application');

        $guardianUser = $this->makeInformationalGuardianUser();

        $this->actingAs($guardianUser)
            ->get('/guardian/info/admission')
            ->assertOk()
            ->assertSeeText('Admission stays an external handoff')
            ->assertSeeText('External application link unavailable')
            ->assertDontSeeText('Open external application');
    }

    public function test_same_host_protected_internal_routes_are_rejected_as_external_admission_destinations(): void
    {
        config([
            'portal.admission.external_url' => 'https://example.test/guardian',
        ]);

        $this->get('/admission')
            ->assertOk()
            ->assertSeeText('External application link unavailable')
            ->assertDontSee('href="https://example.test/guardian"', false);

        $guardianUser = $this->makeInformationalGuardianUser();

        $this->actingAs($guardianUser)
            ->get('/guardian/info/admission')
            ->assertOk()
            ->assertSeeText('External application link unavailable')
            ->assertDontSee('href="https://example.test/guardian"', false);
    }

    private function makeInformationalGuardianUser(): User
    {
        $user = User::factory()->unverified()->create([
            'email' => 'guardian-admission@example.com',
            'approval_status' => User::APPROVAL_NOT_REQUIRED,
            'account_status' => User::ACCOUNT_STATUS_ACTIVE,
        ]);
        $user->assignRole(User::ROLE_REGISTERED_USER);

        Guardian::query()->create([
            'user_id' => $user->id,
            'name' => 'Guardian Admission',
            'email' => $user->email,
            'portal_enabled' => false,
            'isActived' => true,
            'isDeleted' => false,
        ]);

        return $user;
    }
}
