<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;

uses(RefreshDatabase::class);

beforeEach(function () {
    Route::middleware(['web', 'auth', 'role:admin'])->get('/test-admin-route', function () {
        return 'success';
    });
});

test('user helper methods return correct boolean values based on role', function () {
    $admin = User::factory()->make(['role' => 'admin']);
    $doctor = User::factory()->make(['role' => 'doctor']);
    $cashier = User::factory()->make(['role' => 'cashier']);
    $receptionist = User::factory()->make(['role' => 'receptionist']);

    expect($admin->isAdmin())->toBeTrue();
    expect($admin->isDoctor())->toBeFalse();

    expect($doctor->isDoctor())->toBeTrue();
    expect($doctor->isAdmin())->toBeFalse();

    expect($cashier->isCashier())->toBeTrue();
    expect($cashier->isDoctor())->toBeFalse();

    expect($receptionist->isReceptionist())->toBeTrue();
    expect($receptionist->isCashier())->toBeFalse();
});

test('middleware allows user with matching role', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $response = $this->actingAs($admin)->get('/test-admin-route');

    $response->assertStatus(200);
    $response->assertSee('success');
});

test('middleware redirects unauthorized user to dashboard with error', function () {
    $doctor = User::factory()->create(['role' => 'doctor']);

    $response = $this->actingAs($doctor)->get('/test-admin-route');

    $response->assertRedirect(route('dashboard'));
    $response->assertSessionHas('error', 'Akses ditolak.');
});
