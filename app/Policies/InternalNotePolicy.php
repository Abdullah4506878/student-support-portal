<?php

namespace App\Policies;

use App\Enums\RoleName;
use App\Models\Application;
use App\Models\InternalNote;
use App\Models\User;

class InternalNotePolicy
{
    /**
     * Never true for students, under any circumstance.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole([RoleName::AdminOfficer->value, RoleName::SuperAdmin->value]);
    }

    public function view(User $user, InternalNote $internalNote): bool
    {
        return $this->belongsToApplication($user, $internalNote->application);
    }

    /**
     * Checked against the parent application, since the note doesn't
     * exist yet: authorize('create', [InternalNote::class, $application]).
     */
    public function create(User $user, Application $application): bool
    {
        return $this->belongsToApplication($user, $application);
    }

    /**
     * Only the admin who wrote the note, or Super Admin.
     */
    public function update(User $user, InternalNote $internalNote): bool
    {
        return $user->hasRole(RoleName::SuperAdmin->value) || $user->id === $internalNote->admin_id;
    }

    /**
     * No delete — internal notes are permanent once written.
     */
    public function delete(User $user, InternalNote $internalNote): bool
    {
        return false;
    }

    private function belongsToApplication(User $user, Application $application): bool
    {
        if ($user->hasRole(RoleName::SuperAdmin->value)) {
            return true;
        }

        return $user->hasRole(RoleName::AdminOfficer->value)
            && $user->department_id === $application->department_id;
    }
}
