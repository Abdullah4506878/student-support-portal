<?php

namespace App\Models\Scopes;

use App\Enums\RoleName;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

/**
 * Confines an Admin Officer to their own department's rows on any query
 * against a model with a direct department_id column. Super Admin and
 * unauthenticated contexts (console, queue, seeders) are never filtered.
 *
 * @implements Scope<Model>
 */
class AdminDepartmentScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $user = Auth::user();

        if ($user && $user->hasRole(RoleName::AdminOfficer->value)) {
            $builder->where($model->qualifyColumn('department_id'), $user->department_id);
        }
    }
}
