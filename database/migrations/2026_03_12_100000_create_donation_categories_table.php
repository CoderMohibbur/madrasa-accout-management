<?php

use App\Support\Donations\DonationCategorySync;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('donation_categories', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('slug')->unique();
            $table->string('name');
            $table->string('label');
            $table->text('description')->nullable();
            $table->string('badge')->nullable();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->boolean('is_active')->default(true)->index();
            $table->boolean('is_featured')->default(false);
            $table->timestamps();
        });

        app(DonationCategorySync::class)->syncCategories();
    }

    public function down(): void
    {
        Schema::dropIfExists('donation_categories');
    }
};
