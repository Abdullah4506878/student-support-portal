<?php

use App\Models\Announcement;
use App\Models\Department;

beforeEach(function () {
    createRoles();
});

test('a student can view a published active announcement in their own department', function () {
    $department = Department::factory()->create();
    $student = createStudentUser($department);
    $announcement = Announcement::factory()->create([
        'department_id' => $department->id,
        'is_active' => true,
        'publish_at' => now()->subDay(),
        'expires_at' => null,
    ]);

    expect($student->can('view', $announcement))->toBeTrue();
});

test('a student cannot view a draft announcement', function () {
    $department = Department::factory()->create();
    $student = createStudentUser($department);
    $announcement = Announcement::factory()->create([
        'department_id' => $department->id,
        'is_active' => false,
    ]);

    expect($student->can('view', $announcement))->toBeFalse();
});

test('a student cannot view an announcement from another department', function () {
    $departmentA = Department::factory()->create();
    $departmentB = Department::factory()->create();
    $student = createStudentUser($departmentA);
    $announcement = Announcement::factory()->create([
        'department_id' => $departmentB->id,
        'is_active' => true,
    ]);

    expect($student->can('view', $announcement))->toBeFalse();
});

test('an admin officer can view draft announcements in their own department', function () {
    $department = Department::factory()->create();
    $admin = createAdminOfficer($department);
    $announcement = Announcement::factory()->create([
        'department_id' => $department->id,
        'is_active' => false,
    ]);

    expect($admin->can('view', $announcement))->toBeTrue();
});

test('an admin officer cannot update an announcement in another department', function () {
    $departmentA = Department::factory()->create();
    $departmentB = Department::factory()->create();
    $admin = createAdminOfficer($departmentA);
    $announcement = Announcement::factory()->create(['department_id' => $departmentB->id]);

    expect($admin->can('update', $announcement))->toBeFalse();
});

test('announcements can never be deleted, by anyone', function () {
    $department = Department::factory()->create();
    $admin = createAdminOfficer($department);
    $superAdmin = createSuperAdmin();
    $announcement = Announcement::factory()->create(['department_id' => $department->id]);

    expect($admin->can('delete', $announcement))->toBeFalse();
    expect($superAdmin->can('delete', $announcement))->toBeFalse();
});
