<?php

namespace Database\Factories;

use App\Models\Application;
use App\Models\ApplicationAttachment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ApplicationAttachment>
 */
class ApplicationAttachmentFactory extends Factory
{
    protected $model = ApplicationAttachment::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'application_id' => Application::factory(),
            'message_id' => null,
            'uploaded_by' => User::factory(),
            'original_name' => fake()->word().'.pdf',
            'file_path' => 'attachments/'.fake()->uuid().'.pdf',
            'mime_type' => 'application/pdf',
            'size' => fake()->numberBetween(1000, 500000),
        ];
    }
}
