<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambahkan:
     *  - google_id          → untuk Google OAuth login
     *  - wa_reset_otp       → OTP reset password via WA
     *  - wa_reset_otp_expires_at
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Google OAuth
            if (! Schema::hasColumn('users', 'google_id')) {
                $table->string('google_id')->nullable()->unique()->after('email');
            }

            // WA OTP untuk reset password
            if (! Schema::hasColumn('users', 'wa_reset_otp')) {
                $table->string('wa_reset_otp', 6)->nullable()->after('phone_otp_expires_at');
            }
            if (! Schema::hasColumn('users', 'wa_reset_otp_expires_at')) {
                $table->timestamp('wa_reset_otp_expires_at')->nullable()->after('wa_reset_otp');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $cols = ['google_id', 'wa_reset_otp', 'wa_reset_otp_expires_at'];
            $existing = array_filter($cols, fn($c) => Schema::hasColumn('users', $c));
            if ($existing) {
                $table->dropColumn(array_values($existing));
            }
        });
    }
};
