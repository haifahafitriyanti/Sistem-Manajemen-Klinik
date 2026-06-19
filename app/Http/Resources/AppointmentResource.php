<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AppointmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'queue_number' => $this->queue_number,
            'patient_name' => $this->patient_name,
            'date' => $this->date->format('Y-m-d'),
            'time_slot' => $this->time_slot,
            'status' => $this->status,
            'doctor' => $this->whenLoaded('doctor', fn () => [
                'name' => $this->doctor->name,
                'specialization' => $this->doctor->specialization,
            ]),
        ];
    }
}
