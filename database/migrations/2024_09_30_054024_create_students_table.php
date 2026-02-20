<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();

            // ✅ Phase 1: full_name + father_name (first/last বাদ)
            $table->string('full_name')->nullable();
            $table->string('father_name')->nullable();

            $table->date('dob')->nullable();
            $table->integer('roll')->nullable();

            // ✅ email optional (already nullable)
            $table->string('email')->nullable();
            $table->string('mobile')->nullable();
            $table->string('photo')->nullable();
            $table->string('age')->nullable();

            // ✅ FK columns (nullable)
            $table->unsignedBigInteger('fees_type_id')->nullable();
            $table->unsignedBigInteger('class_id')->nullable();
            $table->unsignedBigInteger('section_id')->nullable();
            $table->unsignedBigInteger('academic_year_id')->nullable();

            $table->decimal('scholarship_amount', 15, 2)->nullable();

            // ✅ safer defaults (না দিলে insert fail হবে না)
            $table->boolean('isActived')->default(true);
            $table->boolean('isDeleted')->default(false);

            // ✅ Foreign keys (set null on delete)
            $table->foreign('fees_type_id')->references('id')->on('add_fess_types')->nullOnDelete();
            $table->foreign('class_id')->references('id')->on('add_classes')->nullOnDelete();
            $table->foreign('section_id')->references('id')->on('add_sections')->nullOnDelete();
            $table->foreign('academic_year_id')->references('id')->on('add_academies')->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
