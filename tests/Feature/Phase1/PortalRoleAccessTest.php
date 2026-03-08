<?php

namespace Tests\Feature\Phase1;

use App\Models\Guardian;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PortalRoleAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::query()->create(['name' => 'management', 'display_name' => 'Management']);
        Role::query()->create(['name' => 'guardian', 'display_name' => 'Guardian']);
        Role::query()->create(['name' => 'donor', 'display_name' => 'Donor']);
    }

    public function test_management_route_requires_the_management_role(): void
    {
        $manager = User::factory()->create();
        $manager->assignRole('management');

        $user = User::factory()->create();

        $this->actingAs($manager)
            ->get('/management/access-control')
            ->assertOk();

        $this->actingAs($user)
            ->get('/management/access-control')
            ->assertForbidden();
    }

    public function test_guardian_and_donor_routes_use_their_dedicated_role_boundaries(): void
    {
        $guardian = User::factory()->create();
        $guardian->assignRole('guardian');

        Guardian::query()->create([
            'user_id' => $guardian->id,
            'name' => 'Guardian Portal User',
            'email' => $guardian->email,
            'portal_enabled' => true,
            'isActived' => true,
            'isDeleted' => false,
        ]);

        $donor = User::factory()->create();
        $donor->assignRole('donor');

        $this->actingAs($guardian)
            ->get('/guardian')
            ->assertOk();

        $this->actingAs($guardian)
            ->get('/donor')
            ->assertForbidden();

        $this->actingAs($donor)
            ->get('/donor')
            ->assertOk();
    }
}
