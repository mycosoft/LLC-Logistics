<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->integer('delivery_time_min')->nullable()->after('expected_delivery_date');
            $table->integer('delivery_time_max')->nullable()->after('delivery_time_min');
            $table->enum('delivery_time_unit', ['days', 'months'])->nullable()->after('delivery_time_max');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->dropColumn(['delivery_time_min', 'delivery_time_max', 'delivery_time_unit']);
        });
    }
};
