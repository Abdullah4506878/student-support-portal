<?php

namespace App\Notifications;

class ApplicationSubmitted extends ApplicationStudentNotification
{
    protected function subject(): string
    {
        return __('Application submitted');
    }

    protected function message(): string
    {
        return __(':number has been submitted and is now awaiting review.', [
            'number' => $this->application->application_no,
        ]);
    }
}
