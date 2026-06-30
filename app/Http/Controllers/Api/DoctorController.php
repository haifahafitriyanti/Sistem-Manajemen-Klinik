<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DoctorResource;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\DoctorSchedule;
use App\Traits\ApiResponds;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DoctorController extends Controller
{
    use ApiResponds;

    /**
     * GET /api/doctors
     *
     * Return all active doctors with their active schedules and category.
     * Optional filters: ?specialization={category-slug}, ?search={name}
     */
    public function index(Request $request): JsonResponse
    {
        $query = Doctor::where('is_active', 1)
            ->with([
                'schedules' => fn ($q) => $q->where('is_active', 1)->orderBy('day_of_week'),
                'category',
            ])
            ->orderBy('name');

        // Filter by category slug (case-insensitive)
        if ($request->filled('specialization')) {
            $slug = strtolower($request->query('specialization'));
            $query->whereHas('category', fn ($q) => $q->whereRaw('LOWER(slug) = ?', [$slug]));
        }

        // Search by name (case-insensitive, partial)
        if ($request->filled('search')) {
            $term = '%'.strtolower($request->query('search')).'%';
            $query->whereRaw('LOWER(name) LIKE ?', [$term]);
        }

        $doctors = $query->get();

        return $this->success(
            DoctorResource::collection($doctors),
            'Doctors retrieved successfully'
        );
    }

    /**
     * GET /api/doctors/{slug}
     *
     * Return full doctor profile by slug; 404 envelope if not found.
     */
    public function show(string $slug): JsonResponse
    {
        $doctor = Doctor::where('is_active', 1)
            ->where('slug', $slug)
            ->with([
                'schedules' => fn ($q) => $q->where('is_active', 1)->orderBy('day_of_week'),
                'category',
            ])
            ->first();

        if (! $doctor) {
            return $this->error('Dokter tidak ditemukan', 404);
        }

        return $this->success(
            new DoctorResource($doctor),
            'Doctor retrieved successfully'
        );
    }

    /**
     * GET /api/doctors/{id}/slots?date=YYYY-MM-DD
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
        $doctor = Doctor::where('is_active', 1)->find($id);

        if (! $doctor) {
            return $this->error('Dokter tidak ditemukan', 404);
        }

        // Map date to Indonesian day name (Senin, Selasa, …)
        $dayName = ucfirst(Carbon::parse($date)->locale('id')->dayName);

        // Find the active schedule for this day
        $schedule = DoctorSchedule::where('doctor_id', $id)
            ->where('day_of_week', $dayName)
            ->where('is_active', 1)
            ->first();

        if (! $schedule) {
            return $this->error('Dokter tidak praktik pada hari ini', 404);
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
                'time'      => $time,
                'available' => ! $isBooked,
            ];

            $cursor->addMinutes($schedule->slot_duration_minutes);
        }

        return $this->success(
            [
                'date'  => $date,
                'day'   => $dayName,
                'slots' => $slots,
            ],
            'Slots retrieved successfully'
        );
    }
}
