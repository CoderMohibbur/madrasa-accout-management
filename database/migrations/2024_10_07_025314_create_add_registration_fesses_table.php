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

            // ✅ Optional but recommended for Settings list title
            $table->string('name')->nullable();

            $table->decimal('monthly_fee', 15, 2)->default(0);
            $table->decimal('boarding_fee', 15, 2)->default(0);
            $table->decimal('management_fee', 15, 2)->default(0);
            $table->decimal('examination_fee', 15, 2)->default(0);
            $table->decimal('other', 15, 2)->default(0);

            $table->foreignId('class_id')->nullable()
                ->constrained('add_classes')
                ->nullOnDelete();

            // ✅ one row per class (if you want)
            $table->unique('class_id');

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
        Schema::dropIfExists('add_registration_fesses');
    }
};
