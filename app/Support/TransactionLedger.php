<?php

namespace App\Support;

class TransactionLedger
{
    // ✅ Income side = credit
    public const INCOME_KEYS = ['student_fee', 'donation', 'income', 'loan_taken'];

    // ✅ Expense side = debit
    public const EXPENSE_KEYS = ['expense', 'loan_repayment'];

    public static function split(string $typeKey, float $amount): array
    {
        if ($amount <= 0) {
            throw new \InvalidArgumentException('Amount must be greater than 0.');
        }

        if (in_array($typeKey, self::INCOME_KEYS, true)) {
            return ['debit' => 0, 'credit' => $amount];
        }

        if (in_array($typeKey, self::EXPENSE_KEYS, true)) {
            return ['debit' => $amount, 'credit' => 0];
        }

        throw new \InvalidArgumentException("Unknown ledger rule for typeKey: {$typeKey}");
    }
}
