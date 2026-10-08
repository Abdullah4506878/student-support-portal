<?php

use App\Models\Announcement;
use App\Models\Application;
use App\Models\ApplicationCategory;
use App\Models\Department;
use App\Models\Student;

beforeEach(function () {
    createRoles();
});

test('an admin officer application query never returns another department rows', function () {
    $departmentA = Department::factory()->create();
    $departmentB = Department::factory()->create();
    $admin = createAdminOfficer($departmentA);
    $studentA = createStudentUser($departmentA);
    $studentB = createStudentUser($departmentB);
    $categoryA = ApplicationCategory::factory()->create(['department_id' => $departmentA->id]);
    $categoryB = ApplicationCategory::factory()->create(['department_id' => $departmentB->id]);

    $applicationA = Application::factory()->create([
        'student_id' => $studentA->student->id,
        'department_id' => $departmentA->id,
        'category_id' => $categoryA->id,
    ]);
    $applicationB = Application::factory()->create([
        'student_id' => $studentB->student->id,
        'department_id' => $departmentB->id,
        'category_id' => $categoryB->id,
    ]);

    $this->actingAs($admin);
    $ids = Application::query()->pluck('id');

    expect($ids)->toContain($applicationA->id);
    expect($ids)->not->toContain($applicationB->id);
});

test('an admin officer student query never returns another department rows', function () {
    $departmentA = Department::factory()->create();
    $departmentB = Department::factory()->create();
    $admin = createAdminOfficer($departmentA);
    $studentAId = createStudentUser($departmentA)->student->id;
    $studentBId = createStudentUser($departmentB)->student->id;

    $this->actingAs($admin);
    $ids = Student::query()->pluck('id');

    expect($ids)->toContain($studentAId);
    expect($ids)->not->toContain($studentBId);
});

test('an admin officer category query is scoped to their department', function () {
    $departmentA = Department::factory()->create();
    $departmentB = Department::factory()->create();
    $admin = createAdminOfficer($departmentA);
    $categoryA = ApplicationCategory::factory()->create(['department_id' => $departmentA->id]);
    $categoryB = ApplicationCategory::factory()->create(['department_id' => $departmentB->id]);

    $this->actingAs($admin);
    $ids = ApplicationCategory::query()->pluck('id');

    expect($ids)->toContain($categoryA->id);
    expect($ids)->not->toContain($categoryB->id);
});

test('an admin officer announcement query is scoped to their department', function () {
    $departmentA = Department::factory()->create();
    $departmentB = Department::factory()->create();
    $admin = createAdminOfficer($departmentA);
    $announcementA = Announcement::factory()->create(['department_id' => $departmentA->id]);
    $announcementB = Announcement::factory()->create(['department_id' => $departmentB->id]);

    $this->actingAs($admin);
    $ids = Announcement::query()->pluck('id');

    expect($ids)->toContain($announcementA->id);
    expect($ids)->not->toContain($announcementB->id);
});

test('a super admin query returns rows from every department', function () {
    $departmentA = Department::factory()->create();
    $departmentB = Department::factory()->create();
    $superAdmin = createSuperAdmin();
    $announcementA = Announcement::factory()->create(['department_id' => $departmentA->id]);
    $announcementB = Announcement::factory()->create(['department_id' => $departmentB->id]);

    $this->actingAs($superAdmin);
    $ids = Announcement::query()->pluck('id');

    expect($ids)->toContain($announcementA->id);
    expect($ids)->toContain($announcementB->id);
});
