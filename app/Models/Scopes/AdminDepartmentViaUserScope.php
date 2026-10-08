<?php

namespace App\Models\Scopes;

use App\Enums\RoleName;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

/**
 * Same confinement as AdminDepartmentScope, for models (such as Student)
 * that don't carry department_id directly but reach it through their
 * related user.
 *
 * @implements Scope<Model>
 */
class AdminDepartmentViaUserScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $user = Auth::user();

        if ($user && $user->hasRole(RoleName::AdminOfficer->value)) {
            $builder->whereHas('user', function (Builder $query) use ($user) {
                $query->where('department_id', $user->department_id);
            });
        }
    }
}
