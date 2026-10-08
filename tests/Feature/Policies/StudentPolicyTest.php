<?php

use App\Models\Department;

beforeEach(function () {
    createRoles();
});

test('a student can view their own profile', function () {
    $student = createStudentUser();

    expect($student->can('view', $student->student))->toBeTrue();
});

test('a student cannot view another student profile', function () {
    $department = Department::factory()->create();
    $student = createStudentUser($department);
    $other = createStudentUser($department);

    expect($student->can('view', $other->student))->toBeFalse();
});

test('an admin officer can view a student profile in their own department', function () {
    $department = Department::factory()->create();
    $admin = createAdminOfficer($department);
    $student = createStudentUser($department);

    expect($admin->can('view', $student->student))->toBeTrue();
});

test('an admin officer cannot view a student profile in another department', function () {
    $departmentA = Department::factory()->create();
    $departmentB = Department::factory()->create();
    $admin = createAdminOfficer($departmentA);
    $student = createStudentUser($departmentB);

    expect($admin->can('view', $student->student))->toBeFalse();
});

test('an admin officer can suspend and reactivate a student in their own department', function () {
    $department = Department::factory()->create();
    $admin = createAdminOfficer($department);
    $student = createStudentUser($department);

    expect($admin->can('suspend', $student->student))->toBeTrue();
    expect($admin->can('reactivate', $student->student))->toBeTrue();
});

test('an admin officer cannot suspend a student in another department', function () {
    $departmentA = Department::factory()->create();
    $departmentB = Department::factory()->create();
    $admin = createAdminOfficer($departmentA);
    $student = createStudentUser($departmentB);

    expect($admin->can('suspend', $student->student))->toBeFalse();
});

test('a super admin can suspend a student in any department', function () {
    $superAdmin = createSuperAdmin();
    $student = createStudentUser();

    expect($superAdmin->can('suspend', $student->student))->toBeTrue();
});

test('a student cannot update another student profile', function () {
    $department = Department::factory()->create();
    $student = createStudentUser($department);
    $other = createStudentUser($department);

    expect($student->can('update', $other->student))->toBeFalse();
    expect($student->can('update', $student->student))->toBeTrue();
});

test('students are never deleted, by anyone', function () {
    $student = createStudentUser();
    $superAdmin = createSuperAdmin();

    expect($superAdmin->can('delete', $student->student))->toBeFalse();
});
