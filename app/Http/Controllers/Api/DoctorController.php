<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DoctorResource;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\DoctorSchedule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Carbon;

class DoctorController extends Controller
{
    /**
     * GET /api/doctors
     *
     * Return all active doctors with their active schedules.
     */
    public function index(): AnonymousResourceCollection
    {
        $doctors = Doctor::where('is_active', 1)
            ->with(['schedules' => fn ($q) => $q->where('is_active', 1)->orderBy('day_of_week')])
            ->orderBy('name')
            ->get();

        return DoctorResource::collection($doctors);
    }

    /**
     * GET /api/doctors/{id}/slots?date=2026-06-15
     *
     * Return available time slots for a doctor on a given date.
     */
    public function availableSlots(Request $request, int $id): JsonResponse
    {
        // Validate query parameter
        $validated = $request->validate([
            'date' => ['required', 'date_format:Y-m-d', 'after_or_equal:today'],
        ]);

        $date = $validated['date'];

        // Resolve doctor
        $doctor = Doctor::where('is_active', 1)->findOrFail($id);

        // Map date to Indonesian day name (Senin, Selasa, …)
        $dayName = ucfirst(Carbon::parse($date)->locale('id')->dayName);

        // Find the active schedule for this day
        $schedule = DoctorSchedule::where('doctor_id', $id)
            ->where('day_of_week', $dayName)
            ->where('is_active', 1)
            ->first();

        if (! $schedule) {
            return response()->json(['message' => 'Dokter tidak praktik pada hari ini'], 404);
        }

        // Generate all slots from start_time to end_time with interval = slot_duration_minutes
        $slots = [];
        $cursor = Carbon::parse($date.' '.$schedule->start_time);
        $end = Carbon::parse($date.' '.$schedule->end_time);

        while ($cursor->lt($end)) {
            $time = $cursor->format('H:i');

            // Check if this slot is already booked (any non-cancelled appointment)
            $isBooked = Appointment::where('doctor_id', $id)
                ->whereDate('date', $date)
                ->where('time_slot', $time)
                ->where('status', '!=', 'cancelled')
                ->exists();

            $slots[] = [
                'time' => $time,
                'available' => ! $isBooked,
            ];

            $cursor->addMinutes($schedule->slot_duration_minutes);
        }

        return response()->json([
            'date' => $date,
            'day' => $dayName,
            'slots' => $slots,
        ]);
    }
}
