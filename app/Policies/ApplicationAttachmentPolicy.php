<?php

namespace App\Policies;

use App\Enums\RoleName;
use App\Models\Application;
use App\Models\ApplicationAttachment;
use App\Models\User;

class ApplicationAttachmentPolicy
{
    public function view(User $user, ApplicationAttachment $attachment): bool
    {
        return $this->belongsToApplication($user, $attachment->application);
    }

    /**
     * Checked against the parent application, since the attachment
     * doesn't exist yet: authorize('create', [ApplicationAttachment::class, $application]).
     */
    public function create(User $user, Application $application): bool
    {
        return $this->belongsToApplication($user, $application);
    }

    private function belongsToApplication(User $user, Application $application): bool
    {
        if ($user->hasRole(RoleName::SuperAdmin->value)) {
            return true;
        }

        if ($user->hasRole(RoleName::AdminOfficer->value)) {
            return $user->department_id === $application->department_id;
        }

        if ($user->hasRole(RoleName::Student->value)) {
            return $user->student?->id === $application->student_id;
        }

        return false;
    }

    /**
     * Attachments are never deleted.
     */
    public function delete(User $user, ApplicationAttachment $attachment): bool
    {
        return false;
    }
}
