<?php

namespace App\Exports\Sheets;

use App\Models\Appointment;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class PasienSheet implements FromCollection, ShouldAutoSize, WithHeadings, WithTitle
{
    public function __construct(
        private readonly string $dateFrom,
        private readonly string $dateTo,
    ) {}

    public function title(): string
    {
        return 'Pasien';
    }

    public function headings(): array
    {
        return ['No', 'Status', 'Jumlah', 'Persentase (%)'];
    }

    public function collection(): Collection
    {
        $from = Carbon::parse($this->dateFrom)->startOfDay()->toDateString();
        $to = Carbon::parse($this->dateTo)->endOfDay()->toDateString();

        $total = Appointment::whereBetween('date', [$from, $to])->count();

        $rows = Appointment::whereBetween('date', [$from, $to])
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->orderBy('status')
            ->get();

        $statusLabels = [
            'waiting' => 'Menunggu',
            'in_progress' => 'Sedang Diperiksa',
            'done' => 'Selesai',
            'cancelled' => 'Dibatalkan',
        ];

        $data = collect();

        foreach ($rows as $index => $row) {
            $percent = $total > 0 ? round(($row->total / $total) * 100, 1) : 0;

            $data->push([
                $index + 1,
                $statusLabels[$row->status] ?? ucfirst($row->status),
                $row->total,
                $percent,
            ]);
        }

        // Summary row
        $data->push(['', 'TOTAL', $total, 100]);

        return $data;
    }
}
