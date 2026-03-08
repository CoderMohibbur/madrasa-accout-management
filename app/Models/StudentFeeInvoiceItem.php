<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentFeeInvoiceItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_fee_invoice_id',
        'fees_type_id',
        'title',
        'description',
        'quantity',
        'unit_amount',
        'line_total',
        'sort_order',
        'metadata',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_amount' => 'decimal:2',
        'line_total' => 'decimal:2',
        'metadata' => 'array',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(StudentFeeInvoice::class, 'student_fee_invoice_id');
    }

    public function feesType(): BelongsTo
    {
        return $this->belongsTo(AddFessType::class, 'fees_type_id');
    }
}
