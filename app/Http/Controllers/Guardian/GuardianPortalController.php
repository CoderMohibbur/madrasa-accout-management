<?php

namespace App\Http\Controllers\Guardian;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\StudentFeeInvoice;
use App\Policies\StudentFeeInvoicePolicy;
use App\Policies\StudentPolicy;
use App\Services\GuardianPortal\GuardianPortalData;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class GuardianPortalController extends Controller
{
    public function __construct(
        private readonly GuardianPortalData $guardianPortalData,
    ) {
    }

    public function index(Request $request): View
    {
        $guardian = $this->guardianPortalData->requireGuardian($request->user());
        $students = $this->guardianPortalData->linkedStudentsQuery($guardian)->get();

        $invoiceQuery = $this->guardianPortalData->invoiceQuery($guardian);
        $paymentQuery = $this->guardianPortalData->paymentQuery($guardian);

        $summary = [
            'student_count' => $students->count(),
            'open_invoice_count' => (clone $invoiceQuery)->where('balance_amount', '>', 0)->count(),
            'outstanding_balance' => (float) (clone $invoiceQuery)->sum('balance_amount'),
            'paid_total' => (float) (clone $paymentQuery)->where('status', 'paid')->sum('amount'),
        ];

        $recentInvoices = (clone $invoiceQuery)
            ->orderByDesc('issued_at')
            ->orderByDesc('id')
            ->limit(5)
            ->get();

        $recentPayments = (clone $paymentQuery)
            ->orderByDesc('paid_at')
            ->orderByDesc('initiated_at')
            ->orderByDesc('id')
            ->limit(5)
            ->get();

        return view('guardian.dashboard', compact(
            'guardian',
            'students',
            'summary',
            'recentInvoices',
            'recentPayments',
        ));
    }

    public function student(Request $request, Student $student): View
    {
        abort_unless(app(StudentPolicy::class)->view($request->user(), $student), 403);

        $guardian = $this->guardianPortalData->requireGuardian($request->user());

        $student = $this->guardianPortalData->linkedStudentsQuery($guardian)
            ->whereKey($student->getKey())
            ->firstOrFail();

        $invoices = $this->guardianPortalData->invoiceQuery($guardian)
            ->where('student_id', $student->getKey())
            ->orderByDesc('issued_at')
            ->orderByDesc('id')
            ->get();

        $payments = $this->guardianPortalData->paymentQuery($guardian)
            ->whereHasMorph('payable', [StudentFeeInvoice::class], function ($query) use ($student): void {
                $query->where('student_id', $student->getKey());
            })
            ->orderByDesc('paid_at')
            ->orderByDesc('initiated_at')
            ->orderByDesc('id')
            ->limit(10)
            ->get();

        return view('guardian.student', compact(
            'guardian',
            'student',
            'invoices',
            'payments',
        ));
    }

    public function invoices(Request $request): View
    {
        $guardian = $this->guardianPortalData->requireGuardian($request->user());
        $invoiceQuery = $this->guardianPortalData->invoiceQuery($guardian);

        $summary = [
            'invoice_count' => (clone $invoiceQuery)->count(),
            'overdue_count' => (clone $invoiceQuery)
                ->whereDate('due_at', '<', now()->toDateString())
                ->where('balance_amount', '>', 0)
                ->count(),
            'total_balance' => (float) (clone $invoiceQuery)->sum('balance_amount'),
        ];

        $invoices = $invoiceQuery
            ->withCount(['items', 'payments'])
            ->orderByDesc('issued_at')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('guardian.invoices.index', compact(
            'guardian',
            'summary',
            'invoices',
        ));
    }

    public function invoice(Request $request, StudentFeeInvoice $invoice): View
    {
        abort_unless(app(StudentFeeInvoicePolicy::class)->view($request->user(), $invoice), 403);

        $guardian = $this->guardianPortalData->requireGuardian($request->user());

        $invoice = $this->guardianPortalData->invoiceQuery($guardian)
            ->whereKey($invoice->getKey())
            ->firstOrFail();

        return view('guardian.invoices.show', compact(
            'guardian',
            'invoice',
        ));
    }

    public function history(Request $request): View
    {
        $guardian = $this->guardianPortalData->requireGuardian($request->user());
        $paymentQuery = $this->guardianPortalData->paymentQuery($guardian);

        $summary = [
            'payment_count' => (clone $paymentQuery)->count(),
            'paid_count' => (clone $paymentQuery)->where('status', 'paid')->count(),
            'receipt_count' => (clone $paymentQuery)->has('receipt')->count(),
            'paid_total' => (float) (clone $paymentQuery)->where('status', 'paid')->sum('amount'),
        ];

        $payments = $paymentQuery
            ->orderByDesc('paid_at')
            ->orderByDesc('initiated_at')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('guardian.history', compact(
            'guardian',
            'summary',
            'payments',
        ));
    }
}
