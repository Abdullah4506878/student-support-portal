<?php

namespace App\Models\Concerns;

use App\Models\Scopes\AdminDepartmentViaUserScope;

trait ScopedToAdminDepartmentViaUser
{
    protected static function bootScopedToAdminDepartmentViaUser(): void
    {
        static::addGlobalScope(new AdminDepartmentViaUserScope);
    }
}
