<?php

namespace App\Policies;

use App\Enums\RoleName;
use App\Models\Student;
use App\Models\User;

class StudentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole([
            RoleName::Student->value,
            RoleName::AdminOfficer->value,
            RoleName::SuperAdmin->value,
        ]);
    }

    public function view(User $user, Student $student): bool
    {
        if ($user->hasRole(RoleName::SuperAdmin->value)) {
            return true;
        }

        if ($user->hasRole(RoleName::AdminOfficer->value)) {
            return $user->department_id === $student->user->department_id;
        }

        return $user->id === $student->user_id;
    }

    /**
     * The student may only update their own profile (current_semester,
     * enforced in the Form Request — not here).
     */
    public function update(User $user, Student $student): bool
    {
        return $user->id === $student->user_id;
    }

    /**
     * Super Admin for any department; Admin Officer for their own.
     */
    public function suspend(User $user, Student $student): bool
    {
        return $this->managedBy($user, $student);
    }

    public function reactivate(User $user, Student $student): bool
    {
        return $this->managedBy($user, $student);
    }

    /**
     * Students are never deleted.
     */
    public function delete(User $user, Student $student): bool
    {
        return false;
    }

    private function managedBy(User $user, Student $student): bool
    {
        if ($user->hasRole(RoleName::SuperAdmin->value)) {
            return true;
        }

        return $user->hasRole(RoleName::AdminOfficer->value)
            && $user->department_id === $student->user->department_id;
    }
}
