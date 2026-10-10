<?php

namespace App\Notifications;

class ApplicationClosed extends ApplicationStudentNotification
{
    protected function subject(): string
    {
        return __('Application closed');
    }

    protected function message(): string
    {
        return __(':number has been closed.', ['number' => $this->application->application_no]);
    }
}
