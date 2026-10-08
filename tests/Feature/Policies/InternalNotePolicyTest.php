<?php

use App\Models\Application;
use App\Models\ApplicationCategory;
use App\Models\Department;
use App\Models\InternalNote;

beforeEach(function () {
    createRoles();
});

function createInternalNote(Department $department, $author, $student): InternalNote
{
    $category = ApplicationCategory::factory()->create(['department_id' => $department->id]);
    $application = Application::factory()->create([
        'student_id' => $student->student->id,
        'department_id' => $department->id,
        'category_id' => $category->id,
    ]);

    return InternalNote::factory()->create([
        'application_id' => $application->id,
        'admin_id' => $author->id,
    ]);
}

test('a student can never view internal notes', function () {
    $department = Department::factory()->create();
    $admin = createAdminOfficer($department);
    $student = createStudentUser($department);
    $note = createInternalNote($department, $admin, $student);

    expect($student->can('view', $note))->toBeFalse();
    expect($student->can('viewAny', InternalNote::class))->toBeFalse();
});

test('an admin officer can view internal notes for their own department', function () {
    $department = Department::factory()->create();
    $admin = createAdminOfficer($department);
    $student = createStudentUser($department);
    $note = createInternalNote($department, $admin, $student);

    expect($admin->can('view', $note))->toBeTrue();
});

test('an admin officer cannot view internal notes for another department', function () {
    $departmentA = Department::factory()->create();
    $departmentB = Department::factory()->create();
    $outsider = createAdminOfficer($departmentA);
    $author = createAdminOfficer($departmentB);
    $student = createStudentUser($departmentB);
    $note = createInternalNote($departmentB, $author, $student);

    expect($outsider->can('view', $note))->toBeFalse();
});

test('only the authoring admin or a super admin can update a note', function () {
    $department = Department::factory()->create();
    $author = createAdminOfficer($department);
    $otherAdmin = createAdminOfficer($department);
    $superAdmin = createSuperAdmin();
    $student = createStudentUser($department);
    $note = createInternalNote($department, $author, $student);

    expect($author->can('update', $note))->toBeTrue();
    expect($otherAdmin->can('update', $note))->toBeFalse();
    expect($superAdmin->can('update', $note))->toBeTrue();
});

test('internal notes can never be deleted, by anyone', function () {
    $department = Department::factory()->create();
    $admin = createAdminOfficer($department);
    $student = createStudentUser($department);
    $note = createInternalNote($department, $admin, $student);
    $superAdmin = createSuperAdmin();

    expect($admin->can('delete', $note))->toBeFalse();
    expect($superAdmin->can('delete', $note))->toBeFalse();
});
