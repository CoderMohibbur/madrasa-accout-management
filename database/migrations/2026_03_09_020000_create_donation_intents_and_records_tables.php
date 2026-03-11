<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('donation_intents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('donor_id')->nullable()->constrained()->nullOnDelete();
            $table->string('donor_mode', 40)->index();
            $table->string('display_mode', 40)->default('identified');
            $table->decimal('amount', 15, 2);
            $table->string('currency', 8)->default('BDT');
            $table->string('status', 40)->default('open')->index();
            $table->string('public_reference')->unique();
            $table->string('guest_access_token_hash')->nullable()->index();
            $table->string('name_snapshot')->nullable();
            $table->string('email_snapshot')->nullable();
            $table->string('phone_snapshot')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('settled_at')->nullable();
            $table->timestamps();
        });

        Schema::create('donation_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('donation_intent_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignId('winning_payment_id')->unique()->constrained('payments')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('donor_id')->nullable()->constrained()->nullOnDelete();
            $table->string('donor_mode', 40)->index();
            $table->string('display_mode', 40)->default('identified');
            $table->decimal('amount', 15, 2);
            $table->string('currency', 8)->default('BDT');
            $table->timestamp('donated_at')->nullable();
            $table->string('posting_status', 40)->default('skipped')->index();
            $table->string('name_snapshot')->nullable();
            $table->string('email_snapshot')->nullable();
            $table->string('phone_snapshot')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('donation_records');
        Schema::dropIfExists('donation_intents');
    }
};
