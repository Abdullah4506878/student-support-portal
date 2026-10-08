<?php

namespace App\Policies;

use App\Enums\RoleName;
use App\Models\Announcement;
use App\Models\User;

class AnnouncementPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole([
            RoleName::Student->value,
            RoleName::AdminOfficer->value,
            RoleName::SuperAdmin->value,
        ]);
    }

    public function view(User $user, Announcement $announcement): bool
    {
        if ($user->hasRole(RoleName::SuperAdmin->value)) {
            return true;
        }

        if ($user->hasRole(RoleName::AdminOfficer->value)) {
            return $user->department_id === $announcement->department_id;
        }

        // Students only ever see published, active, in-window announcements
        // for their own department — drafts stay admin-only.
        return $user->department_id === $announcement->department_id
            && $announcement->is_active
            && ($announcement->publish_at === null || $announcement->publish_at->isPast())
            && ($announcement->expires_at === null || $announcement->expires_at->isFuture());
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole([RoleName::AdminOfficer->value, RoleName::SuperAdmin->value]);
    }

    public function update(User $user, Announcement $announcement): bool
    {
        if ($user->hasRole(RoleName::SuperAdmin->value)) {
            return true;
        }

        return $user->hasRole(RoleName::AdminOfficer->value)
            && $user->department_id === $announcement->department_id;
    }

    /**
     * No delete — deactivate via is_active instead.
     */
    public function delete(User $user, Announcement $announcement): bool
    {
        return false;
    }
}
