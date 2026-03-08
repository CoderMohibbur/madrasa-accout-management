<?php

namespace Tests\Feature\Phase1;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class FoundationSchemaTest extends TestCase
{
    use RefreshDatabase;

    public function test_phase_1_tables_and_columns_exist(): void
    {
        $this->assertTrue(Schema::hasTable('roles'));
        $this->assertTrue(Schema::hasTable('permissions'));
        $this->assertTrue(Schema::hasTable('role_user'));
        $this->assertTrue(Schema::hasTable('permission_role'));
        $this->assertTrue(Schema::hasTable('guardians'));
        $this->assertTrue(Schema::hasTable('guardian_student'));
        $this->assertTrue(Schema::hasTable('student_fee_invoices'));
        $this->assertTrue(Schema::hasTable('student_fee_invoice_items'));
        $this->assertTrue(Schema::hasTable('payments'));
        $this->assertTrue(Schema::hasTable('payment_gateway_events'));
        $this->assertTrue(Schema::hasTable('receipts'));
        $this->assertTrue(Schema::hasTable('audit_logs'));

        $this->assertTrue(Schema::hasColumns('donors', [
            'user_id',
            'portal_enabled',
            'address',
            'notes',
        ]));
    }
}
