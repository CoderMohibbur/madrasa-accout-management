<?php

namespace Tests\Unit\Finance;

use App\Services\Finance\CanonicalPostingService;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CanonicalPostingServiceTest extends TestCase
{
    #[Test]
    public function it_builds_inflow_payloads_as_credit_entries(): void
    {
        $payload = (new CanonicalPostingService())->build(
            typeKey: 'student_fee',
            amount: 1500.00,
            sourceType: 'student_fee_invoice',
            sourceId: 10,
            reference: 'INV-10',
        );

        $this->assertSame('inflow', $payload->direction);
        $this->assertSame(0.0, $payload->debit);
        $this->assertSame(1500.0, $payload->credit);
    }

    #[Test]
    public function it_builds_outflow_payloads_as_debit_entries(): void
    {
        $payload = (new CanonicalPostingService())->build(
            typeKey: 'expense',
            amount: 700.00,
            sourceType: 'expense_voucher',
        );

        $this->assertSame('outflow', $payload->direction);
        $this->assertSame(700.0, $payload->debit);
        $this->assertSame(0.0, $payload->credit);
    }

    #[Test]
    public function it_rejects_a_blank_source_type(): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new CanonicalPostingService())->build(
            typeKey: 'student_fee',
            amount: 100.00,
            sourceType: ' ',
        );
    }
}
