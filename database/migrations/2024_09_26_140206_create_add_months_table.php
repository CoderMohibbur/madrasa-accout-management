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
        Schema::create('add_months', function (Blueprint $table) {
            $table->id();
            $table->string('name');              // January
            $table->unsignedTinyInteger('month_no')->nullable(); // 1..12 (optional)
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
        Schema::dropIfExists('add_months');
    }
};
