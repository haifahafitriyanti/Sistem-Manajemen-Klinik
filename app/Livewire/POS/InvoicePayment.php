<?php

namespace App\Livewire\POS;

use App\Models\Invoice;
use Livewire\Component;

class InvoicePayment extends Component
{
    public int $invoiceId;

    public Invoice $invoice;

    public float $discount = 0;

    public float $computedTotal = 0;

    public string $paymentMethod = 'cash';

    public string $paymentStatus = 'fully_paid';

    public string $notes = '';

    /**
     * Mount the component.
     */
    public function mount(int $invoiceId): void
    {
        if (! in_array(auth()->user()->role, ['admin', 'cashier'])) {
            abort(403);
        }

        $this->invoiceId = $invoiceId;
        $this->invoice = Invoice::with(['appointment.doctor'])->findOrFail($invoiceId);
        $this->discount = $this->invoice->discount;
        $this->computedTotal = $this->invoice->subtotal - $this->discount;
        $this->notes = $this->invoice->notes ?? '';
    }

    /**
     * Recompute total when discount changes.
     */
    public function updatedDiscount(): void
    {
        $this->discount = max(0, (float) $this->discount);
        $this->computedTotal = max(0, $this->invoice->subtotal - $this->discount);
    }

    /**
     * Validation rules.
     *
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        return [
            'discount' => ['required', 'numeric', 'min:0', 'max:'.$this->invoice->subtotal],
            'paymentMethod' => ['required', 'in:cash,transfer'],
            'paymentStatus' => ['required', 'in:partially_paid,fully_paid'],
            'notes' => ['nullable', 'string'],
        ];
    }

    /**
     * Process the payment.
     */
    public function confirmPayment(): void
    {
        if (! in_array(auth()->user()->role, ['admin', 'cashier'])) {
            abort(403);
        }

        if ($this->invoice->payment_status === 'fully_paid') {
            return;
        }

        $this->validate();

        $this->invoice->update([
            'cashier_id' => auth()->id(),
            'discount' => $this->discount,
            'total_amount' => max(0, $this->invoice->subtotal - $this->discount),
            'payment_method' => $this->paymentMethod,
            'payment_status' => $this->paymentStatus,
            'paid_at' => now(),
            'notes' => $this->notes ?: null,
        ]);

        session()->flash('success', 'Pembayaran berhasil dicatat.');

        $this->redirect(route('pos.index'));
    }

    /**
     * Render the component.
     */
    public function render()
    {
        if (! in_array(auth()->user()->role, ['admin', 'cashier'])) {
            abort(403);
        }

        return view('livewire.pos.invoice-payment', [
            'invoice' => $this->invoice,
        ])->layout('layouts.app');
    }
}
