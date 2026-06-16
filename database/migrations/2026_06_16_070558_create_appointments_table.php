<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->constrained('doctors')->cascadeOnDelete();
            $table->string('patient_name', 100);
            $table->string('patient_id', 20)->nullable();
            $table->string('phone', 20);
            $table->date('date');
            $table->string('time_slot', 20);
            $table->text('complaint')->nullable();
            $table->text('diagnosis')->nullable();
            $table->enum('status', ['waiting', 'in_progress', 'done', 'cancelled'])->default('waiting');
            $table->integer('queue_number')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index('doctor_id');
            $table->index('date');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
