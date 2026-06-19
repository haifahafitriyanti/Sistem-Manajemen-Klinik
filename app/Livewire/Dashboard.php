<?php

namespace App\Livewire;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Invoice;
use Illuminate\Support\Carbon;
use Livewire\Component;

class Dashboard extends Component
{
    /**
     * Render the dashboard with all widget data.
     */
    public function render()
    {
        $today = today();
        $todayDayName = $this->getTodayDayName();

        // --- Baris 1: Statistik antrian hari ini ---
        $totalToday = Appointment::today()->count();
        $totalWaiting = Appointment::today()->where('status', 'waiting')->count();
        $totalInProgress = Appointment::today()->where('status', 'in_progress')->count();
        $totalDone = Appointment::today()->where('status', 'done')->count();

        // --- Baris 2: Finansial (admin only) ---
        $financialData = null;
        if (auth()->user()->role === 'admin') {
            $financialData = $this->getFinancialData();
        }

        // --- Baris 3: Dokter aktif hari ini ---
        $activeDoctors = Doctor::active()
            ->whereHas('schedules', fn ($q) => $q->where('day_of_week', $todayDayName)->where('is_active', true))
            ->with(['schedules' => fn ($q) => $q->where('day_of_week', $todayDayName)->where('is_active', true)])
            ->orderBy('name')
            ->get()
            ->map(function (Doctor $doctor) use ($today) {
                $doctor->patients_today = Appointment::where('doctor_id', $doctor->id)
                    ->whereDate('date', $today)
                    ->count();

                return $doctor;
            });

        // --- Baris 4: 5 appointment terbaru hari ini ---
        $recentAppointments = Appointment::with('doctor')
            ->today()
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        return view('livewire.dashboard', compact(
            'totalToday',
            'totalWaiting',
            'totalInProgress',
            'totalDone',
            'financialData',
            'activeDoctors',
            'recentAppointments',
        ))->layout('layouts.app');
    }

    /**
     * Compute financial widget data for admin role.
     *
     * @return array{
     *   revenue_this_month: float,
     *   revenue_last_month: float,
     *   change_percent: float|null,
     *   change_direction: string,
     *   unpaid_count: int
     * }
     */
    private function getFinancialData(): array
    {
        $revenueThisMonth = Invoice::where('payment_status', 'fully_paid')
            ->whereMonth('paid_at', now()->month)
            ->whereYear('paid_at', now()->year)
            ->sum('total_amount');

        $lastMonth = now()->subMonth();
        $revenueLastMonth = Invoice::where('payment_status', 'fully_paid')
            ->whereMonth('paid_at', $lastMonth->month)
            ->whereYear('paid_at', $lastMonth->year)
            ->sum('total_amount');

        $changePercent = null;
        $changeDirection = 'neutral';

        if ($revenueLastMonth > 0) {
            $changePercent = (($revenueThisMonth - $revenueLastMonth) / $revenueLastMonth) * 100;
            $changeDirection = match (true) {
                $changePercent > 0 => 'up',
                $changePercent < 0 => 'down',
                default => 'neutral',
            };
        } elseif ($revenueThisMonth > 0) {
            $changeDirection = 'up';
        }

        $unpaidCount = Invoice::where('payment_status', 'unpaid')->count();

        return [
            'revenue_this_month' => $revenueThisMonth,
            'revenue_last_month' => $revenueLastMonth,
            'change_percent' => $changePercent,
            'change_direction' => $changeDirection,
            'unpaid_count' => $unpaidCount,
        ];
    }

    /**
     * Map PHP's day-of-week integer to the Indonesian day name stored in doctor_schedules.
     */
    private function getTodayDayName(): string
    {
        return match (now()->dayOfWeek) {
            Carbon::MONDAY => 'Senin',
            Carbon::TUESDAY => 'Selasa',
            Carbon::WEDNESDAY => 'Rabu',
            Carbon::THURSDAY => 'Kamis',
            Carbon::FRIDAY => 'Jumat',
            Carbon::SATURDAY => 'Sabtu',
            Carbon::SUNDAY => 'Minggu',
        };
    }
}
