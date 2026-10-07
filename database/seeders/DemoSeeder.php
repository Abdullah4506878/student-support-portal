<?php

namespace Database\Seeders;

use App\Enums\ApplicationPriority;
use App\Enums\ApplicationStatus;
use App\Enums\RoleName;
use App\Enums\UserStatus;
use App\Models\Application;
use App\Models\ApplicationCategory;
use App\Models\Department;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Sample students and applications for UI testing.
 *
 * Not called from DatabaseSeeder — run explicitly with:
 *   php artisan db:seed --class=DemoSeeder
 */
class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $department = Department::query()->where('code', 'SE')->firstOrFail();
        $categories = ApplicationCategory::query()->where('department_id', $department->id)->get();
        $statuses = ApplicationStatus::cases();
        $priorities = ApplicationPriority::cases();

        for ($i = 1; $i <= 10; $i++) {
            $user = User::query()->create([
                'name' => fake()->name(),
                'email' => "student{$i}@superior.edu.pk",
                'password' => 'Password123!',
                'department_id' => $department->id,
                'status' => UserStatus::Active,
                'email_verified_at' => now(),
            ]);
            $user->syncRoles([RoleName::Student->value]);

            $student = Student::query()->create([
                'user_id' => $user->id,
                'registration_no' => sprintf('SU92-BSSEM-F22-%03d', $i),
                'program' => 'BS Software Engineering',
                'current_semester' => fake()->numberBetween(1, 8),
                'batch' => 'F22',
            ]);

            foreach (range(1, fake()->numberBetween(1, 3)) as $ignored) {
                Application::createWithApplicationNumber([
                    'student_id' => $student->id,
                    'department_id' => $department->id,
                    'category_id' => $categories->random()->id,
                    'subject' => fake()->sentence(6),
                    'body' => fake()->paragraph(),
                    'semester_at_submission' => $student->current_semester,
                    'priority' => fake()->randomElement($priorities)->value,
                    'status' => fake()->randomElement($statuses)->value,
                ]);
            }
        }
    }
}
