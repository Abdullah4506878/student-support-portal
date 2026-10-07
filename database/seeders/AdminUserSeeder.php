<?php

namespace Database\Seeders;

use App\Enums\RoleName;
use App\Enums\UserStatus;
use App\Models\Department;
use App\Models\User;
use Illuminate\Database\Seeder;
use RuntimeException;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $this->guardAgainstDefaultPasswordsInProduction();

        $department = Department::query()->where('code', 'SE')->firstOrFail();

        $superAdmin = User::query()->updateOrCreate(
            ['email' => config('seeding.super_admin.email')],
            [
                'name' => 'Super Admin',
                'password' => config('seeding.super_admin.password'),
                'department_id' => null,
                'status' => UserStatus::Active,
                'email_verified_at' => now(),
            ],
        );
        $superAdmin->syncRoles([RoleName::SuperAdmin->value]);

        $adminOfficer = User::query()->updateOrCreate(
            ['email' => config('seeding.admin_officer.email')],
            [
                'name' => 'SE Admin Officer',
                'password' => config('seeding.admin_officer.password'),
                'department_id' => $department->id,
                'status' => UserStatus::Active,
                'email_verified_at' => now(),
            ],
        );
        $adminOfficer->syncRoles([RoleName::AdminOfficer->value]);
    }

    private function guardAgainstDefaultPasswordsInProduction(): void
    {
        if (! app()->isProduction()) {
            return;
        }

        $default = config('seeding.default_password');

        if (config('seeding.super_admin.password') === $default
            || config('seeding.admin_officer.password') === $default) {
            throw new RuntimeException(
                'Refusing to seed admin users with the default password in production. '
                .'Set SEED_SUPER_ADMIN_PASSWORD and SEED_ADMIN_OFFICER_PASSWORD in .env.',
            );
        }
    }
}
