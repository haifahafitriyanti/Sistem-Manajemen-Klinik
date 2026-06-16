<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreAppointmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return in_array($this->user()?->role, ['admin', 'receptionist', 'doctor']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'doctor_id' => ['required', 'integer', 'exists:doctors,id'],
            'patient_name' => ['required', 'string', 'max:100'],
            'patient_id' => ['nullable', 'string', 'max:20'],
            'phone' => ['required', 'string', 'max:20'],
            'date' => ['required', 'date', 'after_or_equal:today'],
            'time_slot' => ['required', 'string', 'max:20'],
            'complaint' => ['nullable', 'string'],
        ];
    }

    /**
     * Get human-readable attribute names.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'doctor_id' => 'dokter',
            'patient_name' => 'nama pasien',
            'patient_id' => 'nomor identitas',
            'phone' => 'nomor telepon',
            'date' => 'tanggal',
            'time_slot' => 'slot waktu',
            'complaint' => 'keluhan',
        ];
    }
}
