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
        Schema::create('add_academies', function (Blueprint $table) {
            $table->id();

            $table->string('year');           // "2025"
            $table->string('academic_years'); // "2025-2026"
            $table->date('starting_date');
            $table->date('ending_date');

            $table->boolean('isActived')->default(true);
            $table->boolean('isDeleted')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('add_academies');
    }
};
