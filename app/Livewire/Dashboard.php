<?php

namespace App\Livewire;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Invoice;
use Livewire\Component;

class Dashboard extends Component
{
    /**
     * Render the component with all dashboard data.
     */
    public function render()
    {
        $today = today();

        // ── Row 1: Today's appointment counts ────────────────────────────────
        $totalToday = Appointment::whereDate('date', $today)->count();
        $waitingCount = Appointment::whereDate('date', $today)->where('status', 'waiting')->count();
        $inProgressCount = Appointment::whereDate('date', $today)->where('status', 'in_progress')->count();
        $doneCount = Appointment::whereDate('date', $today)->where('status', 'done')->count();

        // ── Row 2: Financial widgets (admin only) ─────────────────────────────
        $revenueThisMonth = null;
        $revenueLastMonth = null;
        $revenueTrend = null;
        $unpaidCount = null;

        if (auth()->user()->isAdmin()) {
            $revenueThisMonth = (float) Invoice::query()
                ->where('payment_status', 'fully_paid')
                ->whereYear('paid_at', $today->year)
                ->whereMonth('paid_at', $today->month)
                ->sum('total_amount');

            $lastMonth = $today->copy()->subMonth();
            $revenueLastMonth = (float) Invoice::query()
                ->where('payment_status', 'fully_paid')
                ->whereYear('paid_at', $lastMonth->year)
                ->whereMonth('paid_at', $lastMonth->month)
                ->sum('total_amount');

            $revenueTrend = $this->computeTrend($revenueThisMonth, $revenueLastMonth);

            $unpaidCount = Invoice::where('payment_status', 'unpaid')->count();
        }

        // ── Row 3: Doctors on duty today ─────────────────────────────────────
        $todayDayName = $this->indonesianDayName($today);

        $doctorsToday = Doctor::active()
            ->whereHas('schedules', fn ($q) => $q->where('day_of_week', $todayDayName)->where('is_active', 1))
            ->with(['schedules' => fn ($q) => $q->where('day_of_week', $todayDayName)])
            ->withCount(['appointments as today_appointments_count' => fn ($q) => $q->whereDate('date', $today)])
            ->orderBy('name')
            ->get();

        // ── Row 4: Latest 5 appointments today ───────────────────────────────
        $recentAppointments = Appointment::with('doctor')
            ->whereDate('date', $today)
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        return view('livewire.dashboard', compact(
            'totalToday',
            'waitingCount',
            'inProgressCount',
            'doneCount',
            'revenueThisMonth',
            'revenueLastMonth',
            'revenueTrend',
            'unpaidCount',
            'doctorsToday',
            'todayDayName',
            'recentAppointments',
        ))->layout('layouts.app');
    }

    /**
     * Compute percentage trend between two values.
     *
     * @return array{percent: float, direction: string}
     */
    private function computeTrend(float $current, float $previous): array
    {
        if ($previous == 0) {
            return ['percent' => $current > 0 ? 100.0 : 0.0, 'direction' => $current > 0 ? 'up' : 'flat'];
        }

        $percent = round((($current - $previous) / $previous) * 100, 1);

        return [
            'percent' => abs($percent),
            'direction' => match (true) {
                $percent > 0 => 'up',
                $percent < 0 => 'down',
                default => 'flat',
            },
        ];
    }

    /**
     * Map a Carbon date to the Indonesian day name used in doctor_schedules.
     * Accepts both Carbon and CarbonImmutable instances.
     */
    private function indonesianDayName(\DateTimeInterface $date): string
    {
        // isoWeekday: 1=Monday … 7=Sunday — same API on both Carbon and CarbonImmutable
        $dayNumber = (int) $date->format('N');

        return match ($dayNumber) {
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
            7 => 'Minggu',
            default => 'Senin',
        };
    }
}
