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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('full_name')->nullable();
            $table->date('dob')->nullable();
            $table->integer('roll')->nullable();
            $table->string('email')->nullable();
            $table->string('mobile')->nullable();
            $table->string('photo')->nullable();
            $table->string('age')->nullable();
            $table->unsignedBigInteger('fees_type_id')->nullable();
            $table->unsignedBigInteger('class_id')->nullable();
            $table->unsignedBigInteger('section_id')->nullable();
            $table->unsignedBigInteger('academic_year_id')->nullable();
            $table->boolean('isActived');
            $table->boolean('isDeleted')->default(false);
            $table->foreign('fees_type_id')->references('id')->on('add_fess_types');
            $table->foreign('class_id')->references('id')->on('add_classes');
            $table->foreign('section_id')->references('id')->on('sections');
            $table->foreign('academic_year_id')->references('id')->on('add_academies');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
