<?php

namespace App\Livewire\Report;

use App\Exports\ReportExport;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Invoice;
use Illuminate\Support\Carbon;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ReportIndex extends Component
{
    public string $dateFrom = '';

    public string $dateTo = '';

    public function mount(): void
    {
        if (auth()->user()->role !== 'admin') {
            abort(403);
        }

        $this->dateFrom = now()->startOfMonth()->format('Y-m-d');
        $this->dateTo = now()->format('Y-m-d');
    }

    public function applyFilter(): void
    {
        // Triggers re-render; validation happens in render()
        $this->validate([
            'dateFrom' => 'required|date',
            'dateTo' => 'required|date|after_or_equal:dateFrom',
        ]);
    }

    /**
     * Export laporan ke Excel (3 sheet).
     */
    public function exportReport(): BinaryFileResponse
    {
        $this->validate([
            'dateFrom' => 'required|date',
            'dateTo' => 'required|date|after_or_equal:dateFrom',
        ]);

        $filename = 'laporan_'.$this->dateFrom.'_to_'.$this->dateTo.'.xlsx';

        return Excel::download(new ReportExport($this->dateFrom, $this->dateTo), $filename);
    }

    public function render()
    {
        $from = Carbon::parse($this->dateFrom)->startOfDay();
        $to = Carbon::parse($this->dateTo)->endOfDay();

        // ─── Section A: Laporan Pasien ───────────────────────────────────────
        $totalAppointments = Appointment::whereBetween('date', [$from->toDateString(), $to->toDateString()])->count();

        $statusBreakdown = Appointment::whereBetween('date', [$from->toDateString(), $to->toDateString()])
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->orderBy('status')
            ->get()
            ->map(function ($row) use ($totalAppointments) {
                return [
                    'status' => $row->status,
                    'total' => $row->total,
                    'percent' => $totalAppointments > 0
                        ? round(($row->total / $totalAppointments) * 100, 1)
                        : 0,
                ];
            });

        // ─── Section B: Laporan Pendapatan ───────────────────────────────────
        $totalRevenue = Invoice::where('payment_status', 'fully_paid')
            ->whereBetween('paid_at', [$from, $to])
            ->sum('total_amount');

        $revenueByMethod = Invoice::where('payment_status', 'fully_paid')
            ->whereBetween('paid_at', [$from, $to])
            ->selectRaw('payment_method, COUNT(*) as jumlah_transaksi, SUM(total_amount) as total_nominal')
            ->groupBy('payment_method')
            ->orderByDesc('total_nominal')
            ->get();

        $outstandingCount = Invoice::where('payment_status', 'unpaid')->count();
        $outstandingAmount = Invoice::where('payment_status', 'unpaid')->sum('total_amount');

        // ─── Section C: Performa Dokter ───────────────────────────────────────
        $doctorPerformance = Doctor::select('doctors.id', 'doctors.name', 'doctors.specialization')
            ->withCount([
                'appointments as done_count' => fn ($q) => $q
                    ->where('status', 'done')
                    ->whereBetween('date', [$from->toDateString(), $to->toDateString()]),
            ])
            ->orderByDesc('done_count')
            ->get()
            ->map(function (Doctor $doctor) use ($from, $to) {
                $doctor->revenue_sum = Invoice::where('payment_status', 'fully_paid')
                    ->whereHas('appointment', fn ($q) => $q
                        ->where('doctor_id', $doctor->id)
                        ->where('status', 'done')
                        ->whereBetween('date', [$from->toDateString(), $to->toDateString()])
                    )
                    ->sum('total_amount');

                return $doctor;
            });

        return view('livewire.report.report-index', compact(
            'totalAppointments',
            'statusBreakdown',
            'totalRevenue',
            'revenueByMethod',
            'outstandingCount',
            'outstandingAmount',
            'doctorPerformance',
        ))->layout('layouts.app');
    }
}
