<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'username_changed')) {
                $table->boolean('username_changed')->default(false)->after('username');
            }
            if (!Schema::hasColumn('users', 'email_otp')) {
                $table->string('email_otp', 6)->nullable()->after('email_verified_at');
            }
            if (!Schema::hasColumn('users', 'email_otp_expires_at')) {
                $table->timestamp('email_otp_expires_at')->nullable()->after('email_otp');
            }
            if (!Schema::hasColumn('users', 'phone_verified_at')) {
                $table->timestamp('phone_verified_at')->nullable()->after('phone');
            }
            if (!Schema::hasColumn('users', 'phone_otp')) {
                $table->string('phone_otp', 6)->nullable()->after('phone_verified_at');
            }
            if (!Schema::hasColumn('users', 'phone_otp_expires_at')) {
                $table->timestamp('phone_otp_expires_at')->nullable()->after('phone_otp');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $cols = ['username_changed','email_otp','email_otp_expires_at',
                     'phone_verified_at','phone_otp','phone_otp_expires_at'];
            $existing = array_filter($cols, fn($c) => Schema::hasColumn('users', $c));
            if ($existing) {
                $table->dropColumn(array_values($existing));
            }
        });
    }
};
