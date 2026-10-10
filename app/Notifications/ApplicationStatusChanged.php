<?php

namespace App\Notifications;

use App\Enums\ApplicationStatus;
use App\Models\Application;
use Illuminate\Support\Str;

class ApplicationStatusChanged extends ApplicationStudentNotification
{
    public function __construct(Application $application, public ApplicationStatus $to)
    {
        parent::__construct($application);
    }

    protected function subject(): string
    {
        return __('Status updated');
    }

    protected function message(): string
    {
        return __(':number is now :status.', [
            'number' => $this->application->application_no,
            'status' => Str::headline($this->to->value),
        ]);
    }
}
