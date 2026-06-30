<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('doctors', function (Blueprint $table) {
            $table->string('slug', 120)->nullable()->unique()->after('name');
            $table->text('bio')->nullable()->after('photo');
            $table->unsignedSmallInteger('years_experience')->nullable()->after('bio');
            $table->foreignId('doctor_category_id')
                ->nullable()
                ->after('years_experience')
                ->constrained('doctor_categories')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('doctors', function (Blueprint $table) {
            $table->dropForeign(['doctor_category_id']);
            $table->dropColumn(['slug', 'bio', 'years_experience', 'doctor_category_id']);
        });
    }
};
