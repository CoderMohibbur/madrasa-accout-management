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

            // ✅ Core identity
            $table->string('full_name')->nullable();
            $table->string('father_name')->nullable();

            $table->date('dob')->nullable();
            $table->integer('roll')->nullable();

            // ✅ Contact
            $table->string('email')->nullable();
            $table->string('mobile')->nullable();

            // ✅ Address (profile/print)
            $table->text('address')->nullable();

            $table->string('photo')->nullable();
            $table->string('age')->nullable();

            // ✅ FK columns (nullable)
            $table->unsignedBigInteger('fees_type_id')->nullable();
            $table->unsignedBigInteger('class_id')->nullable();
            $table->unsignedBigInteger('section_id')->nullable();
            $table->unsignedBigInteger('academic_year_id')->nullable();

            $table->decimal('scholarship_amount', 15, 2)->nullable();

            // ✅ Boarding (Phase 3)
            $table->boolean('is_boarding')->default(false);
            $table->date('boarding_start_date')->nullable();
            $table->date('boarding_end_date')->nullable();
            $table->text('boarding_note')->nullable();

            // ✅ Status flags
            $table->boolean('isActived')->default(true);
            $table->boolean('isDeleted')->default(false);

            $table->timestamps();

            /**
             * ✅ INDEXING (Scalable + filter friendly)
             * Most common filters:
             *  - academic_year_id, class_id, section_id
             *  - is_boarding
             *  - search by full_name / roll / mobile
             *  - active/deleted filtering
             */
            $table->index('mobile', 'idx_students_mobile');
            $table->index('email', 'idx_students_email');
            $table->index('roll', 'idx_students_roll');
            $table->index('full_name', 'idx_students_full_name');

            $table->index('fees_type_id', 'idx_students_fees_type');
            $table->index('academic_year_id', 'idx_students_academic_year');
            $table->index('class_id', 'idx_students_class');
            $table->index('section_id', 'idx_students_section');

            // ✅ For student list filters (year+class+section)
            $table->index(['academic_year_id', 'class_id', 'section_id'], 'idx_students_ycs');

            // ✅ For quick lookup inside a class/section/year by roll
            $table->index(['academic_year_id', 'class_id', 'section_id', 'roll'], 'idx_students_ycs_roll');

            // ✅ Boarding list (auto list uses is_boarding=true)
            $table->index('is_boarding', 'idx_students_is_boarding');

            // ✅ Status filtering (you use these flags across project)
            $table->index(['isActived', 'isDeleted'], 'idx_students_status');

            /**
             * ✅ Foreign keys (null on delete)
             * Note: FK in MySQL requires index; we already indexed FK columns above.
             */
            $table->foreign('fees_type_id')->references('id')->on('add_fess_types')->nullOnDelete();
            $table->foreign('class_id')->references('id')->on('add_classes')->nullOnDelete();
            $table->foreign('section_id')->references('id')->on('add_sections')->nullOnDelete();
            $table->foreign('academic_year_id')->references('id')->on('add_academies')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};