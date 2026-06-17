<?php

use App\Livewire\POS\InvoiceIndex;
use App\Livewire\POS\InvoicePayment;
use App\Models\Appointment;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

// ─── Helpers ─────────────────────────────────────────────────────────────────

function posAdmin(): User
{
    return User::factory()->create(['role' => 'admin', 'is_active' => 1]);
}

function posCashier(): User
{
    return User::factory()->create(['role' => 'cashier', 'is_active' => 1]);
}

function posDoctor(): User
{
    return User::factory()->create(['role' => 'doctor', 'is_active' => 1]);
}

// ─── Route access ─────────────────────────────────────────────────────────────

test('admin can visit /pos', function () {
    actingAs(posAdmin())->get('/pos')->assertOk();
});

test('cashier can visit /pos', function () {
    actingAs(posCashier())->get('/pos')->assertOk();
});

test('doctor gets 403 on /pos', function () {
    actingAs(posDoctor())->get('/pos')->assertForbidden();
});

test('unauthenticated user is redirected from /pos', function () {
    $this->get('/pos')->assertRedirect('/login');
});

// ─── Invoice model ────────────────────────────────────────────────────────────

test('Invoice generateInvoiceNumber returns correct format', function () {
    $number = Invoice::generateInvoiceNumber();
    expect($number)->toStartWith('INV-'.date('Ymd').'-');
    expect(strlen($number))->toBe(17); // INV-YYYYMMDD-XXXX = 4+1+8+1+4 = 18 chars... let's just check prefix
});

test('Invoice scopeByStatus filters correctly', function () {
    Invoice::factory()->unpaid()->create();
    Invoice::factory()->fullyPaid()->create();

    expect(Invoice::byStatus('unpaid')->count())->toBe(1);
    expect(Invoice::byStatus('fully_paid')->count())->toBe(1);
});

// ─── markAsDone auto-creates invoice ─────────────────────────────────────────

test('markAsDone creates invoice when none exists', function () {
    $apt = Appointment::factory()->inProgress()->create(['date' => today()]);

    $apt->markAsDone();

    expect(Invoice::where('appointment_id', $apt->id)->exists())->toBeTrue();
    $invoice = Invoice::where('appointment_id', $apt->id)->first();
    expect($invoice->payment_status)->toBe('unpaid');
    expect($invoice->subtotal)->toBe((float) $apt->doctor->consultation_fee);
});

test('markAsDone does not duplicate invoice when called twice', function () {
    $apt = Appointment::factory()->inProgress()->create(['date' => today()]);

    $apt->markAsDone();
    $apt->markAsDone(); // second call should be a no-op for invoice

    expect(Invoice::where('appointment_id', $apt->id)->count())->toBe(1);
});

test('Appointment has invoice relation after markAsDone', function () {
    $apt = Appointment::factory()->inProgress()->create(['date' => today()]);
    $apt->markAsDone();

    expect($apt->invoice)->not->toBeNull();
});

// ─── InvoiceIndex component ───────────────────────────────────────────────────

test('InvoiceIndex lists invoices', function () {
    actingAs(posAdmin());

    $invoice = Invoice::factory()->unpaid()->create();

    Livewire::test(InvoiceIndex::class)
        ->assertSee($invoice->invoice_number);
});

test('InvoiceIndex search filters by invoice number', function () {
    actingAs(posAdmin());

    $inv1 = Invoice::factory()->create(['invoice_number' => 'INV-SEARCH-0001']);
    Invoice::factory()->create(['invoice_number' => 'INV-OTHER-0002']);

    Livewire::test(InvoiceIndex::class)
        ->set('search', 'SEARCH')
        ->assertSee('INV-SEARCH-0001')
        ->assertDontSee('INV-OTHER-0002');
});

test('InvoiceIndex filter by payment_status works', function () {
    actingAs(posAdmin());

    Invoice::factory()->unpaid()->create();
    Invoice::factory()->fullyPaid()->create();

    Livewire::test(InvoiceIndex::class)
        ->set('filterStatus', 'unpaid')
        ->assertSee('Belum Bayar');
});

// ─── InvoicePayment component ─────────────────────────────────────────────────

test('InvoicePayment loads invoice detail', function () {
    actingAs(posAdmin());

    $invoice = Invoice::factory()->unpaid()->create();

    Livewire::test(InvoicePayment::class, ['invoiceId' => $invoice->id])
        ->assertSee($invoice->invoice_number);
});

test('InvoicePayment computedTotal updates when discount changes', function () {
    actingAs(posAdmin());

    $invoice = Invoice::factory()->create(['subtotal' => 200000, 'payment_status' => 'unpaid']);

    Livewire::test(InvoicePayment::class, ['invoiceId' => $invoice->id])
        ->set('discount', 50000)
        ->assertSet('computedTotal', 150000);
});

test('InvoicePayment confirmPayment marks invoice fully_paid', function () {
    $cashier = posCashier();
    actingAs($cashier);

    $invoice = Invoice::factory()->unpaid()->create();

    Livewire::test(InvoicePayment::class, ['invoiceId' => $invoice->id])
        ->set('paymentMethod', 'cash')
        ->set('paymentStatus', 'fully_paid')
        ->set('discount', 0)
        ->call('confirmPayment');

    $invoice->refresh();
    expect($invoice->payment_status)->toBe('fully_paid');
    expect($invoice->cashier_id)->toBe($cashier->id);
    expect($invoice->paid_at)->not->toBeNull();
});

test('InvoicePayment validation rejects discount greater than subtotal', function () {
    actingAs(posAdmin());

    $invoice = Invoice::factory()->create(['subtotal' => 100000, 'payment_status' => 'unpaid']);

    Livewire::test(InvoicePayment::class, ['invoiceId' => $invoice->id])
        ->set('discount', 999999)
        ->set('paymentMethod', 'cash')
        ->set('paymentStatus', 'fully_paid')
        ->call('confirmPayment')
        ->assertHasErrors(['discount']);
});

test('InvoicePayment does not process already fully_paid invoice', function () {
    actingAs(posAdmin());

    $invoice = Invoice::factory()->fullyPaid()->create();
    $originalPaidAt = $invoice->paid_at;

    Livewire::test(InvoicePayment::class, ['invoiceId' => $invoice->id])
        ->call('confirmPayment');

    // paid_at unchanged
    expect($invoice->fresh()->paid_at->toDateTimeString())->toBe($originalPaidAt->toDateTimeString());
});
