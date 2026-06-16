<?php

namespace App\Models;

use Database\Factories\AppointmentFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Appointment extends Model
{
    /** @use HasFactory<AppointmentFactory> */
    use HasFactory;

    /**
     * Disable updated_at — table only has created_at.
     *
     * @var string|null
     */
    const UPDATED_AT = null;

    protected $fillable = [
        'doctor_id',
        'patient_name',
        'patient_id',
        'phone',
        'date',
        'time_slot',
        'complaint',
        'diagnosis',
        'status',
        'queue_number',
        'cancellation_reason',
    ];

    /**
     * Get the casts for the model.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'date' => 'date',
            'queue_number' => 'integer',
        ];
    }

    /**
     * Get the doctor that owns this appointment.
     */
    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    /**
     * Scope: today's appointments.
     */
    public function scopeToday(Builder $query): Builder
    {
        return $query->whereDate('date', today());
    }

    /**
     * Scope: filter by status.
     */
    public function scopeByStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    /**
     * Generate the next queue number for this doctor today.
     * Counts all non-cancelled appointments for the doctor on the same date, adds 1.
     */
    public function generateQueueNumber(): int
    {
        return self::where('doctor_id', $this->doctor_id)
            ->whereDate('date', $this->date)
            ->where('status', '!=', 'cancelled')
            ->count() + 1;
    }
}
