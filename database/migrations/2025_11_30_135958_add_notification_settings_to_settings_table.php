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
        Schema::table('settings', function (Blueprint $table) {
            $table->boolean('notify_status_change_email')->default(true)->after('value');
            $table->boolean('notify_status_change_sms')->default(false)->after('notify_status_change_email');
            $table->boolean('notify_status_change_whatsapp')->default(true)->after('notify_status_change_sms');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn(['notify_status_change_email', 'notify_status_change_sms', 'notify_status_change_whatsapp']);
        });
    }
};
