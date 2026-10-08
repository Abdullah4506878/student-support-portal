<?php

use App\Models\Application;
use App\Models\ApplicationAttachment;
use App\Models\ApplicationCategory;
use App\Models\Department;

beforeEach(function () {
    createRoles();
});

function createApplicationAttachment(Department $department, $student): ApplicationAttachment
{
    $category = ApplicationCategory::factory()->create(['department_id' => $department->id]);
    $application = Application::factory()->create([
        'student_id' => $student->student->id,
        'department_id' => $department->id,
        'category_id' => $category->id,
    ]);

    return ApplicationAttachment::factory()->create([
        'application_id' => $application->id,
        'uploaded_by' => $student->id,
    ]);
}

test('a student can view an attachment on their own application', function () {
    $department = Department::factory()->create();
    $student = createStudentUser($department);
    $attachment = createApplicationAttachment($department, $student);

    expect($student->can('view', $attachment))->toBeTrue();
});

test('a student cannot view another student attachment by changing the id', function () {
    $department = Department::factory()->create();
    $owner = createStudentUser($department);
    $attacker = createStudentUser($department);
    $attachment = createApplicationAttachment($department, $owner);

    expect($attacker->can('view', $attachment))->toBeFalse();
});

test('an admin officer cannot view an attachment belonging to another department', function () {
    $departmentA = Department::factory()->create();
    $departmentB = Department::factory()->create();
    $admin = createAdminOfficer($departmentA);
    $student = createStudentUser($departmentB);
    $attachment = createApplicationAttachment($departmentB, $student);

    expect($admin->can('view', $attachment))->toBeFalse();
});

test('an admin officer can view an attachment in their own department', function () {
    $department = Department::factory()->create();
    $admin = createAdminOfficer($department);
    $student = createStudentUser($department);
    $attachment = createApplicationAttachment($department, $student);

    expect($admin->can('view', $attachment))->toBeTrue();
});

test('attachments can never be deleted, by anyone', function () {
    $department = Department::factory()->create();
    $student = createStudentUser($department);
    $attachment = createApplicationAttachment($department, $student);
    $superAdmin = createSuperAdmin();

    expect($superAdmin->can('delete', $attachment))->toBeFalse();
});
