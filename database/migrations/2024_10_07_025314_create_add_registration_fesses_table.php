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
        Schema::create('add_registration_fesses', function (Blueprint $table) {
            $table->id();
            $table->decimal('monthly_fee', 15, 2);
            $table->decimal('boarding_fee', 15, 2);
            $table->decimal('management_fee', 15, 2);
            $table->decimal('examination_fee', 15, 2);
            $table->decimal('other', 15, 2);
            $table->unsignedBigInteger('class_id')->nullable();
            $table->foreign('class_id')->references('id')->on('add_classes');
            $table->boolean('isActived');
            $table->boolean('isDeleted')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('add_registration_fesses');
    }
};
