<?php

namespace Database\Factories;

use App\Models\Appointment;
use App\Models\Invoice;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Invoice>
 */
class InvoiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $subtotal = fake()->randomElement([75000, 100000, 150000, 175000, 200000]);

        return [
            'invoice_number' => 'INV-'.fake()->unique()->numerify('########'),
            'appointment_id' => Appointment::factory()->done(),
            'cashier_id' => null,
            'subtotal' => $subtotal,
            'discount' => 0,
            'total_amount' => $subtotal,
            'payment_method' => 'cash',
            'payment_status' => 'unpaid',
            'paid_at' => null,
            'notes' => null,
        ];
    }

    /**
     * State: unpaid.
     */
    public function unpaid(): static
    {
        return $this->state(['payment_status' => 'unpaid', 'paid_at' => null, 'cashier_id' => null]);
    }

    /**
     * State: partially paid.
     */
    public function partiallyPaid(): static
    {
        return $this->state([
            'payment_status' => 'partially_paid',
            'paid_at' => now(),
        ]);
    }

    /**
     * State: fully paid.
     */
    public function fullyPaid(): static
    {
        return $this->state([
            'payment_status' => 'fully_paid',
            'paid_at' => now(),
        ]);
    }
}
