<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $defaultSettings = [
            ['key' => 'site_name', 'value' => 'LLC Express Logistics', 'type' => 'text'],
            ['key' => 'site_email', 'value' => 'info@llclogistics.com', 'type' => 'email'],
            ['key' => 'site_phone', 'value' => '+256 703 948463', 'type' => 'text'],
            ['key' => 'site_address', 'value' => 'Kawempe - Tula', 'type' => 'text'],
            ['key' => 'system_currency', 'value' => 'USD', 'type' => 'text'],
            ['key' => 'smtp_host', 'value' => 'smtp.gmail.com', 'type' => 'text'],
            ['key' => 'smtp_port', 'value' => '587', 'type' => 'text'],
            ['key' => 'smtp_username', 'value' => '', 'type' => 'text'],
            ['key' => 'smtp_password', 'value' => '', 'type' => 'text'],
            ['key' => 'smtp_encryption', 'value' => 'tls', 'type' => 'text'],
        ];

        foreach ($defaultSettings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                ['value' => $setting['value'], 'type' => $setting['type']]
            );
        }

        $this->command->info('Default settings created successfully!');
    }
}
