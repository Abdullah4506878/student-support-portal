<?php

namespace App\Policies;

use App\Enums\ApplicationStatus;
use App\Enums\RoleName;
use App\Enums\UserStatus;
use App\Models\Application;
use App\Models\User;

class ApplicationPolicy
{
    /**
     * Listing is always allowed; which rows come back is enforced by
     * query scoping (ScopedToAdminDepartment / the student's own relation),
     * not by this policy.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole([
            RoleName::Student->value,
            RoleName::AdminOfficer->value,
            RoleName::SuperAdmin->value,
        ]);
    }

    public function view(User $user, Application $application): bool
    {
        return $this->ownsOrManages($user, $application);
    }

    public function create(User $user): bool
    {
        return $user->hasRole(RoleName::Student->value) && $user->status === UserStatus::Active;
    }

    public function respondToRequest(User $user, Application $application): bool
    {
        return $user->hasRole(RoleName::Student->value)
            && $user->student?->id === $application->student_id
            && ! $this->isFinal($application);
    }

    /**
     * A free-text message, from the Admin Officer/Super Admin managing
     * this application, any time it isn't closed/rejected.
     */
    public function sendMessage(User $user, Application $application): bool
    {
        return $this->manages($user, $application) && ! $this->isFinal($application);
    }

    /**
     * Sending an info or document request — same rule as sendMessage;
     * kept as a separate ability name since it's a distinct action.
     */
    public function sendRequest(User $user, Application $application): bool
    {
        return $this->sendMessage($user, $application);
    }

    public function updateStatus(User $user, Application $application): bool
    {
        return $this->manages($user, $application);
    }

    public function updatePriority(User $user, Application $application): bool
    {
        return $this->manages($user, $application);
    }

    public function reject(User $user, Application $application): bool
    {
        return $this->manages($user, $application);
    }

    public function resolve(User $user, Application $application): bool
    {
        return $this->manages($user, $application);
    }

    public function close(User $user, Application $application): bool
    {
        return $this->manages($user, $application);
    }

    /**
     * Applications are never deleted.
     */
    public function delete(User $user, Application $application): bool
    {
        return false;
    }

    private function ownsOrManages(User $user, Application $application): bool
    {
        if ($user->hasRole(RoleName::Student->value)) {
            return $user->student?->id === $application->student_id;
        }

        return $this->manages($user, $application);
    }

    private function manages(User $user, Application $application): bool
    {
        if ($user->hasRole(RoleName::SuperAdmin->value)) {
            return true;
        }

        return $user->hasRole(RoleName::AdminOfficer->value)
            && $user->department_id === $application->department_id;
    }

    private function isFinal(Application $application): bool
    {
        return in_array($application->status, [ApplicationStatus::Closed, ApplicationStatus::Rejected], true);
    }
}
