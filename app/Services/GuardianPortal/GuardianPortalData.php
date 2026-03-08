<?php

namespace App\Services\GuardianPortal;

use App\Models\Guardian;
use App\Models\Payment;
use App\Models\StudentFeeInvoice;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Symfony\Component\HttpKernel\Exception\HttpException;

class GuardianPortalData
{
    public function requireGuardian(User $user): Guardian
    {
        $guardian = $user->guardianProfile()->first();

        if (! $guardian || ! $guardian->portal_enabled || ! $guardian->isActived || $guardian->isDeleted) {
            throw new HttpException(403, 'Guardian portal access is not enabled for this user.');
        }

        return $guardian;
    }

    public function linkedStudentsQuery(Guardian $guardian)
    {
        return $guardian->students()
            ->with(['class', 'section', 'academicYear', 'feesType'])
            ->orderBy('full_name');
    }

    public function invoiceQuery(Guardian $guardian): Builder
    {
        $query = StudentFeeInvoice::query()
            ->with([
                'student.class',
                'student.section',
                'student.academicYear',
                'items',
                'payments.receipt',
            ]);

        $this->applyInvoiceOwnershipScope($query, $guardian);

        return $query;
    }

    public function paymentQuery(Guardian $guardian): Builder
    {
        return Payment::query()
            ->with([
                'receipt',
                'payable.student.class',
                'payable.student.section',
            ])
            ->whereHasMorph('payable', [StudentFeeInvoice::class], function (Builder $query) use ($guardian): void {
                $this->applyInvoiceOwnershipScope($query, $guardian);
            });
    }

    public function applyInvoiceOwnershipScope(Builder $query, Guardian $guardian): void
    {
        $query
            ->whereIn('student_id', $guardian->students()->select('students.id'))
            ->where(function (Builder $invoiceQuery) use ($guardian): void {
                $invoiceQuery
                    ->whereNull('guardian_id')
                    ->orWhere('guardian_id', $guardian->getKey());
            });
    }
}
