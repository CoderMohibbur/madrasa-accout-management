<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');

            // ✅ NEW: key system (unique)
            $table->string('key')->nullable()->unique();

            $table->boolean('isActived')->default(true);
            $table->boolean('isDeleted')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions_types');
    }
};
