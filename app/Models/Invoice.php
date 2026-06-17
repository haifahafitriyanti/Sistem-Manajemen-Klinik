<?php

namespace App\Models;

use Database\Factories\InvoiceFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends Model
{
    /** @use HasFactory<InvoiceFactory> */
    use HasFactory;

    /**
     * No updated_at — table only has created_at.
     *
     * @var string|null
     */
    const UPDATED_AT = null;

    protected $fillable = [
        'invoice_number',
        'appointment_id',
        'cashier_id',
        'subtotal',
        'discount',
        'total_amount',
        'payment_method',
        'payment_status',
        'paid_at',
        'notes',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'subtotal' => 'float',
            'discount' => 'float',
            'total_amount' => 'float',
            'paid_at' => 'datetime',
        ];
    }

    /**
     * Get the appointment this invoice belongs to.
     */
    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    /**
     * Get the cashier user who processed this invoice.
     */
    public function cashier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    /**
     * Generate a unique invoice number for a new invoice.
     * Format: INV-YYYYMMDD-0001
     */
    public static function generateInvoiceNumber(): string
    {
        $nextId = (self::max('id') ?? 0) + 1;

        return 'INV-'.date('Ymd').'-'.str_pad((string) $nextId, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Scope: filter by payment status.
     */
    public function scopeByStatus(Builder $query, string $status): Builder
    {
        return $query->where('payment_status', $status);
    }
}
