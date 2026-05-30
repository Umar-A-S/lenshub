<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('rentals', 'user_id')) {

            Schema::table('rentals', function (Blueprint $table) {

                $table->foreignId('user_id')
                    ->nullable()
                    ->after('client_id')
                    ->constrained()
                    ->nullOnDelete();

            });

        }
    }

    public function down(): void
    {
        Schema::table('rentals', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\User::class);
            $table->dropColumn('user_id');
        });
    }
};