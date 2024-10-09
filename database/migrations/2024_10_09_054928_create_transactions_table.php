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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('student_id')->nullable();
            $table->unsignedBigInteger('doner_id')->nullable();
            $table->unsignedBigInteger('lender_id')->nullable();
            $table->string('student_book_number')->nullable();
            $table->unsignedBigInteger('fees_type_id')->nullable();
            $table->unsignedBigInteger('section_id')->nullable();
            $table->unsignedBigInteger('academic_year_id')->nullable();
            $table->integer('recipt_no')->nullable();
            $table->decimal('monthly_fees', 15, 2)->nullable();
            $table->decimal('boarding_fees', 15, 2)->nullable();
            $table->decimal('management_fees', 15, 2)->nullable();
            $table->decimal('exam_fees', 15, 2)->nullable();
            $table->decimal('others_fees', 15, 2)->nullable();
            $table->decimal('total_fees', 15, 2)->nullable();
            $table->decimal('debit', 15, 2)->nullable();
            $table->decimal('credit', 15, 2)->nullable();
            $table->date('transactions_date')->nullable();
            $table->unsignedBigInteger('account_id')->nullable();
            $table->unsignedBigInteger('class_id')->nullable();
            $table->unsignedBigInteger('months_id')->nullable();
            $table->unsignedBigInteger('created_by_id')->nullable();
            $table->string('noth')->nullable();
            $table->decimal('c_d_1', 15, 2)->nullable();
            $table->decimal('c_d_2', 15, 2)->nullable();
            $table->decimal('c_d_3', 15, 2)->nullable();
            $table->decimal('c_d_4', 15, 2)->nullable();
            $table->decimal('c_d_5', 15, 2)->nullable();
            $table->decimal('c_d_6', 15, 2)->nullable();
            $table->decimal('c_d_7', 15, 2)->nullable();
            $table->string('c_s_1')->nullable();
            $table->string('c_s_2')->nullable();
            $table->string('c_s_3')->nullable();
            $table->string('c_s_4')->nullable();
            $table->string('c_s_5')->nullable();
            $table->string('c_s_6')->nullable();
            $table->string('c_s_7')->nullable();
            $table->string('c_s_8')->nullable();
            $table->integer('c_i_1')->nullable();
            $table->integer('c_i_2')->nullable();
            $table->integer('c_i_3')->nullable();
            $table->integer('c_i_4')->nullable();
            $table->integer('c_i_5')->nullable();
            $table->integer('c_i_6')->nullable();





            $table->foreign('student_id')->references('id')->on('students');
            $table->foreign('doner_id')->references('id')->on('donors');
            $table->foreign('lender_id')->references('id')->on('lenders');
            $table->foreign('fees_type_id')->references('id')->on('add_fess_types');
            $table->foreign('section_id')->references('id')->on('add_sections');
            $table->foreign('academic_year_id')->references('id')->on('add_academies');
            $table->foreign('account_id')->references('id')->on('accounts');
            $table->foreign('class_id')->references('id')->on('add_classes');
            $table->foreign('months_id')->references('id')->on('add_months');
            $table->foreign('created_by_id')->references('id')->on('users');
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
        Schema::dropIfExists('transactions');
    }
};
