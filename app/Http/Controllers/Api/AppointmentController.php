<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreAppointmentRequest;
use App\Http\Resources\AppointmentResource;
use App\Models\Appointment;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class AppointmentController extends Controller
{
    /**
     * POST /api/appointments
     *
     * Book a new appointment with double-booking protection inside a transaction.
     */
    public function store(StoreAppointmentRequest $request): AppointmentResource|JsonResponse
    {
        $data = $request->validated();

        /** @var Appointment|null $appointment */
        $appointment = DB::transaction(function () use ($data) {
            // Re-check slot availability inside the transaction to prevent race conditions.
            // Locking the conflicting rows so concurrent requests must wait.
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

            // Persist first so generateQueueNumber() can count correctly within the transaction
            $appointment->save();

            $appointment->queue_number = $appointment->generateQueueNumber();
            $appointment->save();

            return $appointment->load('doctor');
        });

        if ($appointment === null) {
            return response()->json(
                ['message' => 'Slot waktu tersebut sudah diambil, silakan pilih slot lain'],
                409
            );
        }

        return (new AppointmentResource($appointment))
            ->response()
            ->setStatusCode(201);
    }
}
