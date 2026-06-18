<?php

namespace App\Livewire\POS;

use App\Exports\InvoicesExport;
use App\Models\Invoice;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

class InvoiceIndex extends Component
{
    use WithPagination;

    public string $search = '';

    public string $filterStatus = '';

    public string $filterDate = '';

    public string $exportDateFrom = '';

    public string $exportDateTo = '';

    public function mount(): void
    {
        $this->exportDateFrom = now()->startOfMonth()->format('Y-m-d');
        $this->exportDateTo = now()->format('Y-m-d');
    }

    /**
     * Reset pagination when filters change.
     */
    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedFilterStatus(): void
    {
        $this->resetPage();
    }

    public function updatedFilterDate(): void
    {
        $this->resetPage();
    }

    /**
     * Export invoices to Excel.
     */
    public function exportExcel(): mixed
    {
        $filename = 'invoices_'.$this->exportDateFrom.'_to_'.$this->exportDateTo.'.xlsx';

        return Excel::download(
            new InvoicesExport($this->exportDateFrom, $this->exportDateTo, $this->filterStatus ?: null),
            $filename,
        );
    }

    /**
     * Render the component.
     */
    public function render()
    {
        if (! in_array(auth()->user()->role, ['admin', 'cashier'])) {
            abort(403);
        }

        $invoices = Invoice::with(['appointment.doctor'])
            ->when($this->filterStatus, fn ($q) => $q->where('payment_status', $this->filterStatus))
            ->when($this->filterDate, fn ($q) => $q->whereDate('created_at', $this->filterDate))
            ->when($this->search, function ($q) {
                $q->where(function ($inner) {
                    $inner->where('invoice_number', 'like', '%'.$this->search.'%')
                        ->orWhereHas('appointment', fn ($r) => $r->where('patient_name', 'like', '%'.$this->search.'%'));
                });
            })
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('livewire.pos.invoice-index', compact('invoices'))
            ->layout('layouts.app');
    }
}
