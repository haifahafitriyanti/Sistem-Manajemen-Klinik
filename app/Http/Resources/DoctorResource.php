<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class DoctorResource extends JsonResource
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
            'name' => $this->name,
            'specialization' => $this->specialization,
            'consultation_fee' => (int) $this->consultation_fee,
            'photo' => $this->photo ? Storage::url($this->photo) : null,
            'schedules' => DoctorScheduleResource::collection(
                $this->whenLoaded('schedules')
            ),
        ];
    }
}
