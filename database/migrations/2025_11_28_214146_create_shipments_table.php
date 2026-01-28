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
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->string('tracking_number')->unique();
            $table->string('origin');
            $table->string('destination');
            $table->decimal('weight', 10, 2)->nullable();
            $table->enum('shipment_type', ['air', 'sea', 'road']);
            $table->string('current_status')->default('pending');
            $table->text('description')->nullable();
            $table->date('expected_delivery_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};
