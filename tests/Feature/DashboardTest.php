<?php

use App\Enums\RoleName;
use App\Models\User;

beforeEach(function () {
    createRoles();
});

test('guests are redirected to the login page', function () {
    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('login'));
});

test('a user with no role sees the generic placeholder dashboard', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('dashboard'));
    $response->assertOk();
});

test('a student is redirected to the student dashboard', function () {
    $user = User::factory()->create();
    $user->assignRole(RoleName::Student->value);
    $this->actingAs($user);

    $this->get(route('dashboard'))->assertRedirect(route('student.dashboard'));
});

test('an admin officer is redirected to the admin dashboard', function () {
    $user = User::factory()->create();
    $user->assignRole(RoleName::AdminOfficer->value);
    $this->actingAs($user);

    $this->get(route('dashboard'))->assertRedirect(route('admin.dashboard'));
});

test('a super admin is redirected to the super admin dashboard', function () {
    $user = User::factory()->create();
    $user->assignRole(RoleName::SuperAdmin->value);
    $this->actingAs($user);

    $this->get(route('dashboard'))->assertRedirect(route('super-admin.dashboard'));
});
