<?php

namespace App\Models\Concerns;

use App\Models\Scopes\AdminDepartmentScope;

trait ScopedToAdminDepartment
{
    protected static function bootScopedToAdminDepartment(): void
    {
        static::addGlobalScope(new AdminDepartmentScope);
    }
}
