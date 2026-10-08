<?php

use App\Enums\RoleName;
use App\Models\User;

beforeEach(function () {
    createRoles();
});

test('a guest hitting an admin route is redirected to login and back to it after authenticating', function () {
    $response = $this->get(route('admin.dashboard'));
    $response->assertRedirect(route('login'));

    $user = User::factory()->create();
    $user->assignRole(RoleName::AdminOfficer->value);

    $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'password',
    ])->assertRedirect(route('admin.dashboard'));
});

test('a student cannot access the admin dashboard', function () {
    $user = User::factory()->create();
    $user->assignRole(RoleName::Student->value);

    $this->actingAs($user)->get(route('admin.dashboard'))->assertForbidden();
});

test('a student cannot access the super admin dashboard', function () {
    $user = User::factory()->create();
    $user->assignRole(RoleName::Student->value);

    $this->actingAs($user)->get(route('super-admin.dashboard'))->assertForbidden();
});

test('an admin officer cannot access the student dashboard', function () {
    $user = User::factory()->create();
    $user->assignRole(RoleName::AdminOfficer->value);

    $this->actingAs($user)->get(route('student.dashboard'))->assertForbidden();
});

test('an admin officer cannot access the super admin dashboard', function () {
    $user = User::factory()->create();
    $user->assignRole(RoleName::AdminOfficer->value);

    $this->actingAs($user)->get(route('super-admin.dashboard'))->assertForbidden();
});

test('a super admin cannot access the student or admin dashboards', function () {
    $user = User::factory()->create();
    $user->assignRole(RoleName::SuperAdmin->value);

    $this->actingAs($user)->get(route('student.dashboard'))->assertForbidden();
    $this->actingAs($user)->get(route('admin.dashboard'))->assertForbidden();
});

test('each role can reach its own dashboard', function () {
    $student = User::factory()->create();
    $student->assignRole(RoleName::Student->value);
    $this->actingAs($student)->get(route('student.dashboard'))->assertOk();

    $admin = User::factory()->create();
    $admin->assignRole(RoleName::AdminOfficer->value);
    $this->actingAs($admin)->get(route('admin.dashboard'))->assertOk();

    $superAdmin = User::factory()->create();
    $superAdmin->assignRole(RoleName::SuperAdmin->value);
    $this->actingAs($superAdmin)->get(route('super-admin.dashboard'))->assertOk();
});
