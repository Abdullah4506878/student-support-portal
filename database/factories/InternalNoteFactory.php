<?php

namespace Database\Factories;

use App\Models\Application;
use App\Models\InternalNote;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<InternalNote>
 */
class InternalNoteFactory extends Factory
{
    protected $model = InternalNote::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'application_id' => Application::factory(),
            'admin_id' => User::factory(),
            'body' => fake()->paragraph(),
        ];
    }
}
