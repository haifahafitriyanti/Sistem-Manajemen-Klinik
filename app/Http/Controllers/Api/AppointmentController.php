<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreAppointmentRequest;
use App\Http\Resources\AppointmentResource;
use App\Models\Appointment;
use App\Traits\ApiResponds;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class AppointmentController extends Controller
{
    use ApiResponds;

    /**
     * POST /api/appointments
     *
     * Book a new appointment with double-booking protection inside a transaction.
     * Accepts preferred_date / preferred_time and maps them internally.
     */
    public function store(StoreAppointmentRequest $request): JsonResponse
    {
        $data = $request->validated();

        /** @var Appointment|null $appointment */
        $appointment = DB::transaction(function () use ($data) {
            // Re-check slot availability inside the transaction to prevent race conditions.
            $conflict = Appointment::where('doctor_id', $data['doctor_id'])
                ->whereDate('date', $data['date'])
                ->where('time_slot', $data['time_slot'])
                ->where('status', '!=', 'cancelled')
                ->lockForUpdate()
                ->exists();

            if ($conflict) {
                return null;
            }

            $appointment = new Appointment($data);
            $appointment->status = 'waiting';
            $appointment->save();

            $appointment->queue_number = $appointment->generateQueueNumber();
            $appointment->save();

            return $appointment->load('doctor');
        });

        if ($appointment === null) {
            return $this->error(
                'Slot waktu tersebut sudah diambil, silakan pilih slot lain',
                409
            );
        }

        return $this->success(
            new AppointmentResource($appointment),
            'Appointment booked successfully',
            201
        );
    }
}
