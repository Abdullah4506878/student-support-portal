<?php

namespace Database\Factories;

use App\Models\Announcement;
use App\Models\Department;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Announcement>
 */
class AnnouncementFactory extends Factory
{
    protected $model = Announcement::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'department_id' => Department::factory(),
            'created_by' => User::factory(),
            'title' => fake()->sentence(4),
            'body' => fake()->paragraph(),
            'image_path' => null,
            'attachment_path' => null,
            'publish_at' => null,
            'expires_at' => null,
            'is_active' => true,
        ];
    }
}
