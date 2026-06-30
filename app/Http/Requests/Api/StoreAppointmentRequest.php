<?php

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAppointmentRequest extends FormRequest
{
    /**
     * Public endpoint — always authorized.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Validation rules.
     * Accepts preferred_date / preferred_time (public API) or date / time_slot (legacy).
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'doctor_id'      => [
                'required',
                'integer',
                Rule::exists('doctors', 'id')->where('is_active', 1),
            ],
            'patient_name'   => ['required', 'string', 'max:100'],
            'patient_id'     => ['nullable', 'string', 'max:20'],
            'phone'          => ['required', 'string', 'max:20'],
            'email'          => ['nullable', 'email', 'max:150'],
            // Accept either preferred_date (new) or date (legacy)
            'preferred_date' => ['required_without:date', 'date', 'after_or_equal:today'],
            'date'           => ['required_without:preferred_date', 'date', 'after_or_equal:today'],
            // Accept either preferred_time (new) or time_slot (legacy)
            'preferred_time' => ['required_without:time_slot', 'string'],
            'time_slot'      => ['required_without:preferred_time', 'string'],
            'complaint'      => ['nullable', 'string'],
        ];
    }

    /**
     * Normalise the data: merge preferred_date -> date, preferred_time -> time_slot.
     * This runs after validation passes, so downstream code always sees `date` and `time_slot`.
     *
     * @return array<string, mixed>
     */
    public function validated($key = null, $default = null): mixed
    {
        /** @var array<string, mixed> $data */
        $data = parent::validated($key, $default);

        // Map public API field names to internal column names
        if (isset($data['preferred_date']) && ! isset($data['date'])) {
            $data['date'] = $data['preferred_date'];
        }
        if (isset($data['preferred_time']) && ! isset($data['time_slot'])) {
            $data['time_slot'] = $data['preferred_time'];
        }

        // Remove the public-facing aliases; they are not table columns
        unset($data['preferred_date'], $data['preferred_time']);

        return $data;
    }

    /**
     * Human-readable attribute names for validation messages.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'doctor_id'      => 'dokter',
            'patient_name'   => 'nama pasien',
            'patient_id'     => 'nomor identitas',
            'phone'          => 'nomor telepon',
            'email'          => 'email',
            'preferred_date' => 'tanggal kunjungan',
            'date'           => 'tanggal',
            'preferred_time' => 'jam kunjungan',
            'time_slot'      => 'slot waktu',
            'complaint'      => 'keluhan',
        ];
    }
}
