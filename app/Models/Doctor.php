<?php

namespace App\Models;

use Database\Factories\DoctorFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Doctor extends Model
{
    /** @use HasFactory<DoctorFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'specialization',
        'license_number',
        'phone',
        'consultation_fee',
        'photo',
        'bio',
        'years_experience',
        'doctor_category_id',
        'is_active',
    ];

    /**
     * Get the casts for the model.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'consultation_fee' => 'float',
            'is_active' => 'boolean',
            'years_experience' => 'integer',
        ];
    }

    /**
     * Get the category this doctor belongs to.
     */
    public function category(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(DoctorCategory::class, 'doctor_category_id');
    }

    /**
     * Get the schedules for the doctor.
     */
    public function schedules(): HasMany
    {
        return $this->hasMany(DoctorSchedule::class);
    }

    /**
     * Get the appointments for the doctor.
     */
    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    /**
     * Scope a query to only include active doctors.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', 1);
    }
}
