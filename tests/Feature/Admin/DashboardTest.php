<?php

use App\Enums\ApplicationPriority;
use App\Enums\ApplicationStatus;
use App\Models\Application;
use App\Models\ApplicationCategory;
use App\Models\Department;

beforeEach(function () {
    createRoles();
});

test('the dashboard shows real kpi counts scoped to the admin\'s department', function () {
    $department = Department::factory()->create();
    $admin = createAdminOfficer($department);
    $student = createStudentUser($department);
    $category = ApplicationCategory::factory()->create(['department_id' => $department->id]);

    Application::factory()->create([
        'student_id' => $student->student->id,
        'department_id' => $department->id,
        'category_id' => $category->id,
        'status' => ApplicationStatus::Submitted,
    ]);
    Application::factory()->create([
        'student_id' => $student->student->id,
        'department_id' => $department->id,
        'category_id' => $category->id,
        'status' => ApplicationStatus::InProgress,
        'priority' => ApplicationPriority::Urgent,
    ]);

    // An application in another department must never affect these counts.
    $otherDepartment = Department::factory()->create();
    $otherStudent = createStudentUser($otherDepartment);
    $otherCategory = ApplicationCategory::factory()->create(['department_id' => $otherDepartment->id]);
    Application::factory()->create([
        'student_id' => $otherStudent->student->id,
        'department_id' => $otherDepartment->id,
        'category_id' => $otherCategory->id,
        'status' => ApplicationStatus::Submitted,
    ]);

    $response = $this->actingAs($admin)->get(route('admin.dashboard'));

    $response->assertOk();
    $response->assertViewHas('stats', function (array $stats) {
        return $stats['new'] === 1 && $stats['in_progress'] === 1 && $stats['urgent'] === 1;
    });
});
