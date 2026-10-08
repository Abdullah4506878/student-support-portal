<?php

namespace Database\Factories;

use App\Enums\ApplicationPriority;
use App\Enums\ApplicationStatus;
use App\Models\Application;
use App\Models\ApplicationCategory;
use App\Models\Department;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Application>
 */
class ApplicationFactory extends Factory
{
    protected $model = Application::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'application_no' => null,
            'student_id' => Student::factory(),
            'department_id' => Department::factory(),
            'category_id' => ApplicationCategory::factory(),
            'subject' => fake()->sentence(6),
            'body' => fake()->paragraph(),
            'semester_at_submission' => fake()->numberBetween(1, 8),
            'priority' => ApplicationPriority::Normal,
            'status' => ApplicationStatus::Submitted,
        ];
    }
}
