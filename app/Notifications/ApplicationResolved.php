<?php

namespace App\Notifications;

use App\Models\Application;

class ApplicationResolved extends ApplicationStudentNotification
{
    public function __construct(Application $application, public string $resolutionNote)
    {
        parent::__construct($application);
    }

    protected function subject(): string
    {
        return __('Application resolved');
    }

    protected function message(): string
    {
        return __(':number has been resolved.', ['number' => $this->application->application_no]);
    }

    protected function extraLines(): array
    {
        return [__('Resolution note: :note', ['note' => $this->resolutionNote])];
    }
}
