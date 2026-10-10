<?php

namespace App\Notifications;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

/**
 * Admin Officers get a database notification only — no email — when a
 * new application lands in their own department's queue.
 */
class NewApplicationSubmitted extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Application $application) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'application_id' => $this->application->id,
            'application_no' => $this->application->application_no,
            'message' => __('New application :number from :name.', [
                'number' => $this->application->application_no,
                'name' => $this->application->student->user->name,
            ]),
            'url' => route('admin.applications.show', $this->application),
        ];
    }
}
