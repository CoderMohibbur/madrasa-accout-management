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
    public function resolveAccess(User $user): GuardianProtectedAccessState
    {
        $guardian = $user->guardianProfile()->first();
        $profileEligible = ! is_null($guardian)
            && $guardian->portal_enabled
            && $guardian->isActived
            && ! $guardian->isDeleted;
        $hasLinkedStudents = ! is_null($guardian)
            && $guardian->students()->exists();
        $protectedEligible = $user->hasAccessibleAccountState()
            && $user->hasVerifiedEmail()
            && $profileEligible
            && $hasLinkedStudents;

        $reason = match (true) {
            ! $user->hasAccessibleAccountState() => 'account_blocked',
            ! $guardian && $user->hasRole('guardian') => 'role_only',
            ! $guardian => 'none',
            $guardian->isDeleted => 'profile_deleted',
            ! $guardian->isActived => 'profile_inactive',
            ! $guardian->portal_enabled => 'profile_pending',
            ! $user->hasVerifiedEmail() => 'email_unverified',
            ! $hasLinkedStudents => 'unlinked',
            default => 'protected_eligible',
        };

        return new GuardianProtectedAccessState(
            hasAccessibleAccountState: $user->hasAccessibleAccountState(),
            hasVerifiedEmail: $user->hasVerifiedEmail(),
            profileEligible: $profileEligible,
            hasLinkedStudents: $hasLinkedStudents,
            protectedEligible: $protectedEligible,
            guardian: $guardian,
            reason: $reason,
        );
    }

    public function requireProtectedAccess(User $user): GuardianProtectedAccessState
    {
        $access = $this->resolveAccess($user);

        if (! $access->protectedEligible || ! $access->guardian) {
            throw new HttpException(403, 'Guardian protected portal access is not enabled for this user.');
        }

        return $access;
    }

    public function shouldUseProtectedHome(User $user): bool
    {
        if ($user->hasRole('management')) {
            return false;
        }

        return $this->resolveAccess($user)->protectedEligible;
    }

    public function requireGuardian(User $user): Guardian
    {
        $access = $this->requireProtectedAccess($user);

        return $access->guardian;
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
