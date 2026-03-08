<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->foreignId('reviewed_by_user_id')
                ->nullable()
                ->after('user_id')
                ->constrained('users')
                ->nullOnDelete();
            $table->foreignId('posted_transaction_id')
                ->nullable()
                ->after('payable_id')
                ->constrained('transactions')
                ->nullOnDelete();
            $table->string('provider_mode', 20)->nullable()->after('provider')->index();
            $table->string('verification_status', 40)->default('pending')->after('status')->index();
            $table->string('status_reason')->nullable()->after('verification_status');
            $table->timestamp('verified_at')->nullable()->after('paid_at');
            $table->timestamp('cancelled_at')->nullable()->after('failed_at');
            $table->timestamp('reviewed_at')->nullable()->after('cancelled_at');
        });

        Schema::table('payment_gateway_events', function (Blueprint $table) {
            $table->string('provider_order_id')->nullable()->after('provider_event_id')->index();
            $table->string('request_source', 40)->nullable()->after('event_name')->index();
            $table->unsignedSmallInteger('http_status')->nullable()->after('request_source');
        });
    }

    public function down(): void
    {
        Schema::table('payment_gateway_events', function (Blueprint $table) {
            $table->dropColumn([
                'provider_order_id',
                'request_source',
                'http_status',
            ]);
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('reviewed_by_user_id');
            $table->dropConstrainedForeignId('posted_transaction_id');
            $table->dropColumn([
                'provider_mode',
                'verification_status',
                'status_reason',
                'verified_at',
                'cancelled_at',
                'reviewed_at',
            ]);
        });
    }
};
