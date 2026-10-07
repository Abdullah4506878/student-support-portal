<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        Setting::set('applications.subject_max_length', '100');
        Setting::set('applications.body_max_length', '500');
    }
}
