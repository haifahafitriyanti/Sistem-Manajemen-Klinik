<?php

namespace App\Exports\Sheets;

use App\Models\Invoice;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class PendapatanSheet implements FromCollection, ShouldAutoSize, WithHeadings, WithTitle
{
    public function __construct(
        private readonly string $dateFrom,
        private readonly string $dateTo,
    ) {}

    public function title(): string
    {
        return 'Pendapatan';
    }

    public function headings(): array
    {
        return ['No', 'Metode Pembayaran', 'Jumlah Transaksi', 'Total Nominal (Rp)'];
    }

    public function collection(): Collection
    {
        $from = Carbon::parse($this->dateFrom)->startOfDay();
        $to = Carbon::parse($this->dateTo)->endOfDay();

        $rows = Invoice::where('payment_status', 'fully_paid')
            ->whereBetween('paid_at', [$from, $to])
            ->selectRaw('payment_method, COUNT(*) as jumlah_transaksi, SUM(total_amount) as total_nominal')
            ->groupBy('payment_method')
            ->orderByDesc('total_nominal')
            ->get();

        $data = collect();
        $grandTotal = 0;
        $grandCount = 0;

        foreach ($rows as $index => $row) {
            $data->push([
                $index + 1,
                strtoupper($row->payment_method ?? '-'),
                $row->jumlah_transaksi,
                $row->total_nominal,
            ]);
            $grandTotal += $row->total_nominal;
            $grandCount += $row->jumlah_transaksi;
        }

        // Grand total row
        $data->push(['', 'TOTAL', $grandCount, $grandTotal]);

        return $data;
    }
}
