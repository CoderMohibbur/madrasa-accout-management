<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('incomes', function (Blueprint $table) {
            $table->id();

            $table->string('name');

            // Optional: category link (income breakdown/report)
            $table->unsignedBigInteger('catagory_id')->nullable();

            $table->boolean('isActived')->default(true);
            $table->boolean('isDeleted')->default(false);

            $table->timestamps();

            $table->foreign('catagory_id')->references('id')->on('catagories')->nullOnDelete();

            $table->index(['isActived', 'isDeleted']);
            $table->index('catagory_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incomes');
    }
};
