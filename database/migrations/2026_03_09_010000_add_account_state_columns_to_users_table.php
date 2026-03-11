<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('approval_status', 32)->nullable()->index();
            $table->string('account_status', 32)->nullable()->index();
            $table->string('phone', 32)->nullable()->index();
            $table->timestamp('phone_verified_at')->nullable();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['approval_status']);
            $table->dropIndex(['account_status']);
            $table->dropIndex(['phone']);
            $table->dropColumn([
                'approval_status',
                'account_status',
                'phone',
                'phone_verified_at',
            ]);
            $table->dropSoftDeletes();
        });
    }
};
