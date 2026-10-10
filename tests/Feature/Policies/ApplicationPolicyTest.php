<?php

use App\Enums\ApplicationStatus;
use App\Enums\UserStatus;
use App\Models\Application;
use App\Models\ApplicationCategory;
use App\Models\Department;

beforeEach(function () {
    createRoles();
});

test('a student can view their own application', function () {
    $department = Department::factory()->create();
    $student = createStudentUser($department);
    $category = ApplicationCategory::factory()->create(['department_id' => $department->id]);
    $application = Application::factory()->create([
        'student_id' => $student->student->id,
        'department_id' => $department->id,
        'category_id' => $category->id,
    ]);

    expect($student->can('view', $application))->toBeTrue();
});

test('a student cannot view another student application, even in the same department', function () {
    $department = Department::factory()->create();
    $owner = createStudentUser($department);
    $other = createStudentUser($department);
    $category = ApplicationCategory::factory()->create(['department_id' => $department->id]);
    $application = Application::factory()->create([
        'student_id' => $owner->student->id,
        'department_id' => $department->id,
        'category_id' => $category->id,
    ]);

    expect($other->can('view', $application))->toBeFalse();
});

test('an admin officer can view an application in their own department', function () {
    $department = Department::factory()->create();
    $admin = createAdminOfficer($department);
    $student = createStudentUser($department);
    $category = ApplicationCategory::factory()->create(['department_id' => $department->id]);
    $application = Application::factory()->create([
        'student_id' => $student->student->id,
        'department_id' => $department->id,
        'category_id' => $category->id,
    ]);

    expect($admin->can('view', $application))->toBeTrue();
});

test('an admin officer cannot view an application in another department', function () {
    $departmentA = Department::factory()->create();
    $departmentB = Department::factory()->create();
    $admin = createAdminOfficer($departmentA);
    $student = createStudentUser($departmentB);
    $category = ApplicationCategory::factory()->create(['department_id' => $departmentB->id]);
    $application = Application::factory()->create([
        'student_id' => $student->student->id,
        'department_id' => $departmentB->id,
        'category_id' => $category->id,
    ]);

    expect($admin->can('view', $application))->toBeFalse();
});

test('a super admin can view an application in any department', function () {
    $department = Department::factory()->create();
    $superAdmin = createSuperAdmin();
    $student = createStudentUser($department);
    $category = ApplicationCategory::factory()->create(['department_id' => $department->id]);
    $application = Application::factory()->create([
        'student_id' => $student->student->id,
        'department_id' => $department->id,
        'category_id' => $category->id,
    ]);

    expect($superAdmin->can('view', $application))->toBeTrue();
});

test('only an active student can create an application', function () {
    $activeStudent = createStudentUser();
    $suspendedStudent = createStudentUser();
    $suspendedStudent->update(['status' => UserStatus::Suspended]);
    $admin = createAdminOfficer();

    expect($activeStudent->can('create', Application::class))->toBeTrue();
    expect($suspendedStudent->can('create', Application::class))->toBeFalse();
    expect($admin->can('create', Application::class))->toBeFalse();
});

test('an admin officer cannot update the status of another department application', function () {
    $departmentA = Department::factory()->create();
    $departmentB = Department::factory()->create();
    $admin = createAdminOfficer($departmentA);
    $student = createStudentUser($departmentB);
    $category = ApplicationCategory::factory()->create(['department_id' => $departmentB->id]);
    $application = Application::factory()->create([
        'student_id' => $student->student->id,
        'department_id' => $departmentB->id,
        'category_id' => $category->id,
    ]);

    expect($admin->can('updateStatus', $application))->toBeFalse();
});

test('applications can never be deleted, by anyone', function () {
    $department = Department::factory()->create();
    $student = createStudentUser($department);
    $category = ApplicationCategory::factory()->create(['department_id' => $department->id]);
    $application = Application::factory()->create([
        'student_id' => $student->student->id,
        'department_id' => $department->id,
        'category_id' => $category->id,
    ]);
    $superAdmin = createSuperAdmin();

    expect($superAdmin->can('delete', $application))->toBeFalse();
});

test('an admin officer can send a message or a request on an open application in their own department', function () {
    $department = Department::factory()->create();
    $admin = createAdminOfficer($department);
    $student = createStudentUser($department);
    $category = ApplicationCategory::factory()->create(['department_id' => $department->id]);
    $application = Application::factory()->create([
        'student_id' => $student->student->id,
        'department_id' => $department->id,
        'category_id' => $category->id,
    ]);

    expect($admin->can('sendMessage', $application))->toBeTrue();
    expect($admin->can('sendRequest', $application))->toBeTrue();
});

test('a student can never send a message or a request', function () {
    $department = Department::factory()->create();
    $student = createStudentUser($department);
    $category = ApplicationCategory::factory()->create(['department_id' => $department->id]);
    $application = Application::factory()->create([
        'student_id' => $student->student->id,
        'department_id' => $department->id,
        'category_id' => $category->id,
    ]);

    expect($student->can('sendMessage', $application))->toBeFalse();
    expect($student->can('sendRequest', $application))->toBeFalse();
});

test('sending a message or a request is blocked once the application is closed or rejected', function () {
    $department = Department::factory()->create();
    $admin = createAdminOfficer($department);
    $student = createStudentUser($department);
    $category = ApplicationCategory::factory()->create(['department_id' => $department->id]);

    $closed = Application::factory()->create([
        'student_id' => $student->student->id,
        'department_id' => $department->id,
        'category_id' => $category->id,
        'status' => ApplicationStatus::Closed,
    ]);
    $rejected = Application::factory()->create([
        'student_id' => $student->student->id,
        'department_id' => $department->id,
        'category_id' => $category->id,
        'status' => ApplicationStatus::Rejected,
    ]);

    expect($admin->can('sendMessage', $closed))->toBeFalse();
    expect($admin->can('sendRequest', $closed))->toBeFalse();
    expect($admin->can('sendMessage', $rejected))->toBeFalse();
    expect($admin->can('sendRequest', $rejected))->toBeFalse();
});

test('responding to a request is blocked once the application is closed or rejected', function () {
    $department = Department::factory()->create();
    $student = createStudentUser($department);
    $category = ApplicationCategory::factory()->create(['department_id' => $department->id]);
    $closed = Application::factory()->create([
        'student_id' => $student->student->id,
        'department_id' => $department->id,
        'category_id' => $category->id,
        'status' => ApplicationStatus::Closed,
    ]);

    expect($student->can('respondToRequest', $closed))->toBeFalse();
});
