<?php

namespace Tests\Feature\Phase12;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AccountStateSchemaFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_prompt_29_adds_nullable_first_account_state_columns_to_users(): void
    {
        $this->assertTrue(Schema::hasColumns('users', [
            'email_verified_at',
            'approval_status',
            'account_status',
            'phone',
            'phone_verified_at',
            'deleted_at',
        ]));

        $user = User::factory()->unverified()->create();
        $user->refresh();

        $this->assertNull($user->approval_status);
        $this->assertNull($user->account_status);
        $this->assertNull($user->phone);
        $this->assertNull($user->phone_verified_at);
        $this->assertNull($user->deleted_at);
        $this->assertNull($user->email_verified_at);
    }
}
