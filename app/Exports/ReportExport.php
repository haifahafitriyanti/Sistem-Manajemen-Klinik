<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;

class ReportExport implements WithMultipleSheets
{
    public function __construct(
        private readonly string $dateFrom,
        private readonly string $dateTo,
    ) {}

    /**
     * @return array<int, WithTitle>
     */
    public function sheets(): array
    {
        return [
            new Sheets\PasienSheet($this->dateFrom, $this->dateTo),
            new Sheets\PendapatanSheet($this->dateFrom, $this->dateTo),
            new Sheets\PerformaDokterSheet($this->dateFrom, $this->dateTo),
        ];
    }
}
