<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreMedicalRecordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return in_array($this->user()?->role, ['admin', 'doctor']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'complaint' => ['nullable', 'string'],
            'diagnosis' => ['required', 'string'],
            'prescription' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
        ];
    }

    /**
     * Human-readable attribute names.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'diagnosis' => 'diagnosis',
            'complaint' => 'keluhan',
            'prescription' => 'resep',
            'notes' => 'catatan',
        ];
    }
}
