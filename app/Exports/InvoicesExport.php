<?php

namespace App\Exports;

use App\Models\Invoice;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class InvoicesExport implements FromQuery, ShouldAutoSize, WithHeadings, WithMapping
{
    private int $rowNumber = 0;

    public function __construct(
        private readonly string $dateFrom,
        private readonly string $dateTo,
        private readonly ?string $paymentStatus = null,
    ) {}

    public function query(): Builder
    {
        return Invoice::with(['appointment.doctor', 'cashier'])
            ->whereBetween('created_at', [
                $this->dateFrom.' 00:00:00',
                $this->dateTo.' 23:59:59',
            ])
            ->when($this->paymentStatus, fn ($q) => $q->where('payment_status', $this->paymentStatus))
            ->orderByDesc('created_at');
    }

    public function headings(): array
    {
        return [
            'No',
            'Invoice Number',
            'Nama Pasien',
            'Dokter',
            'Tanggal',
            'Subtotal',
            'Diskon',
            'Total',
            'Metode Bayar',
            'Status',
            'Diproses Oleh',
            'Tanggal Bayar',
        ];
    }

    /**
     * @param  Invoice  $invoice
     */
    public function map($invoice): array
    {
        $this->rowNumber++;

        $statusLabels = [
            'unpaid' => 'Belum Dibayar',
            'partially_paid' => 'Bayar Sebagian',
            'fully_paid' => 'Lunas',
        ];

        return [
            $this->rowNumber,
            $invoice->invoice_number,
            $invoice->appointment->patient_name ?? '-',
            $invoice->appointment->doctor->name ?? '-',
            $invoice->created_at->format('d/m/Y'),
            number_format($invoice->subtotal, 0, ',', '.'),
            number_format($invoice->discount, 0, ',', '.'),
            number_format($invoice->total_amount, 0, ',', '.'),
            $invoice->payment_method ? strtoupper($invoice->payment_method) : '-',
            $statusLabels[$invoice->payment_status] ?? $invoice->payment_status,
            $invoice->cashier->name ?? '-',
            $invoice->paid_at ? $invoice->paid_at->format('d/m/Y') : '-',
        ];
    }
}
