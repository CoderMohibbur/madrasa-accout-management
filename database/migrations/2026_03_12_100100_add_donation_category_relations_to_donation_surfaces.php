<?php

use App\Support\Donations\DonationCategorySync;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('donation_intents', function (Blueprint $table) {
            $table->foreignId('donation_category_id')
                ->nullable()
                ->after('donor_id')
                ->constrained('donation_categories')
                ->nullOnDelete();
        });

        Schema::table('donation_records', function (Blueprint $table) {
            $table->foreignId('donation_category_id')
                ->nullable()
                ->after('donor_id')
                ->constrained('donation_categories')
                ->nullOnDelete();
        });

        $sync = app(DonationCategorySync::class);
        $sync->backfillDonationIntents();
        $sync->backfillDonationRecords();
    }

    public function down(): void
    {
        Schema::table('donation_records', function (Blueprint $table) {
            $table->dropConstrainedForeignId('donation_category_id');
        });

        Schema::table('donation_intents', function (Blueprint $table) {
            $table->dropConstrainedForeignId('donation_category_id');
        });
    }
};
