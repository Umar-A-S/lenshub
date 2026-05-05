<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('rentals', function (Blueprint $table) {
            $table->integer('total_days_late')->default(0)->after('penalty_amount');
            $table->bigInteger('final_amount')->default(0)->after('total_price');
        });
    }

    public function down()
    {
        Schema::table('rentals', function (Blueprint $table) {
            $table->dropColumn(['total_days_late', 'final_amount']);
        });
    }
};
