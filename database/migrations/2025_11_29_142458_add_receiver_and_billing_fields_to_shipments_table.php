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
            $table->foreignId('receiver_id')->nullable()->after('client_id')->constrained('clients')->nullOnDelete();
            $table->decimal('tax', 10, 2)->default(0)->after('insurance_value');
            $table->decimal('discount', 10, 2)->default(0)->after('tax');
            $table->decimal('total_amount', 10, 2)->nullable()->after('discount');
            $table->string('currency', 3)->default('USD')->after('total_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->dropForeign(['receiver_id']);
            $table->dropColumn(['receiver_id', 'tax', 'discount', 'total_amount', 'currency']);
        });
    }
};
