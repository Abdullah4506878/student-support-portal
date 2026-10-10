<?php

namespace App\Notifications;

use App\Models\Application;

class ApplicationRejected extends ApplicationStudentNotification
{
    public function __construct(Application $application, public string $rejectionReason)
    {
        parent::__construct($application);
    }

    protected function subject(): string
    {
        return __('Application rejected');
    }

    protected function message(): string
    {
        return __(':number has been rejected.', ['number' => $this->application->application_no]);
    }

    protected function extraLines(): array
    {
        return [__('Reason: :reason', ['reason' => $this->rejectionReason])];
    }
}
