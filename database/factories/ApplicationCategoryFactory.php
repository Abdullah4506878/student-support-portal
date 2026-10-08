<?php

namespace Database\Factories;

use App\Models\ApplicationCategory;
use App\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ApplicationCategory>
 */
class ApplicationCategoryFactory extends Factory
{
    protected $model = ApplicationCategory::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'department_id' => Department::factory(),
            'name' => fake()->unique()->words(2, true),
            'is_active' => true,
            'sort_order' => 0,
        ];
    }
}
