<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expens', function (Blueprint $table) {
            $table->id();
            $table->string('name');

            // ✅ only this (column + FK)
            $table->foreignId('catagory_id')
                ->nullable()
                ->constrained('catagories')   // আপনার টেবিল নাম যদি catagories হয়
                ->nullOnDelete();

            $table->boolean('isActived')->default(true);
            $table->boolean('isDeleted')->default(false);
            $table->timestamps();

            $table->index(['isActived', 'isDeleted']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expens');
    }
};
