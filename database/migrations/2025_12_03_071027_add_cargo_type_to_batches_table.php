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
        Schema::table('shipment_batches', function (Blueprint $table) {
            $table->enum('cargo_type', ['air', 'sea'])->after('batch_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipment_batches', function (Blueprint $table) {
            $table->dropColumn('cargo_type');
        });
    }
};
