<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ApiSetting;

class ApiSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ApiSetting::create([
            'provider_name' => 'Provider A',
            'api_url' => 'https://provider-a.com/api/availability',
        ]);

        ApiSetting::create([
            'provider_name' => 'Provider B',
            'api_url' => 'https://provider-b.com/api/check',
        ]);

        ApiSetting::create([
            'provider_name' => 'Provider C',
            'api_url' => 'https://provider-c.com/api/services',
            'header_name' => 'X-Address',
        ]);
    }
}
