<?php

namespace Database\Seeders;

use App\Models\ApplicationCategory;
use App\Models\Department;
use Illuminate\Database\Seeder;

class ApplicationCategorySeeder extends Seeder
{
    public function run(): void
    {
        $department = Department::query()->where('code', 'SE')->firstOrFail();

        $categories = [
            'Fee Issue',
            'Attendance Issue',
            'Examination Issue',
            'Result/Grade Issue',
            'Registration Issue',
            'Academic Issue',
            'LMS/Portal Issue',
            'Scholarship/Financial Aid',
            'Timetable Issue',
            'Faculty/Teacher Related',
            'General Query',
            'Other',
        ];

        foreach ($categories as $index => $name) {
            ApplicationCategory::query()->updateOrCreate(
                ['department_id' => $department->id, 'name' => $name],
                ['is_active' => true, 'sort_order' => $index],
            );
        }
    }
}
