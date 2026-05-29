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
            $table->timestamp('ready_for_pickup_at')->nullable()->after('current_status');
            $table->timestamp('auction_notified_at')->nullable()->after('ready_for_pickup_at');
            $table->string('charge_type')->nullable()->after('shipment_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->dropColumn(['ready_for_pickup_at', 'auction_notified_at', 'charge_type']);
        });
    }
};
