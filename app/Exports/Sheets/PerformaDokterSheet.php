<?php

namespace App\Exports\Sheets;

use App\Models\Doctor;
use App\Models\Invoice;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class PerformaDokterSheet implements FromCollection, ShouldAutoSize, WithHeadings, WithTitle
{
    public function __construct(
        private readonly string $dateFrom,
        private readonly string $dateTo,
    ) {}

    public function title(): string
    {
        return 'Performa Dokter';
    }

    public function headings(): array
    {
        return ['No', 'Nama Dokter', 'Spesialisasi', 'Jumlah Pasien Selesai', 'Total Revenue (Rp)'];
    }

    public function collection(): Collection
    {
        $from = Carbon::parse($this->dateFrom)->startOfDay()->toDateString();
        $to = Carbon::parse($this->dateTo)->endOfDay()->toDateString();

        $doctors = Doctor::select('id', 'name', 'specialization')
            ->withCount([
                'appointments as done_count' => fn ($q) => $q
                    ->where('status', 'done')
                    ->whereBetween('date', [$from, $to]),
            ])
            ->orderByDesc('done_count')
            ->get()
            ->map(fn (Doctor $doctor) => [
                'name' => $doctor->name,
                'specialization' => $doctor->specialization,
                'done_count' => $doctor->done_count,
                'revenue_sum' => Invoice::where('payment_status', 'fully_paid')
                    ->whereHas('appointment', fn ($q) => $q
                        ->where('doctor_id', $doctor->id)
                        ->where('status', 'done')
                        ->whereBetween('date', [$from, $to])
                    )
                    ->sum('total_amount'),
            ]);

        return $doctors->values()->map(function (array $row, int $index) {
            return [
                $index + 1,
                $row['name'],
                $row['specialization'],
                $row['done_count'],
                $row['revenue_sum'],
            ];
        });
    }
}
