<?php

use App\Enums\ApplicationStatus;
use App\Livewire\Admin\Applications\Index;
use App\Models\Application;
use App\Models\ApplicationCategory;
use App\Models\Department;
use Livewire\Livewire;

beforeEach(function () {
    createRoles();
});

test('an admin officer only sees applications from their own department', function () {
    $departmentA = Department::factory()->create();
    $departmentB = Department::factory()->create();
    $admin = createAdminOfficer($departmentA);

    $studentA = createStudentUser($departmentA);
    $categoryA = ApplicationCategory::factory()->create(['department_id' => $departmentA->id]);
    $ownApplication = Application::factory()->create([
        'student_id' => $studentA->student->id,
        'department_id' => $departmentA->id,
        'category_id' => $categoryA->id,
        'subject' => 'Own department application',
    ]);

    $studentB = createStudentUser($departmentB);
    $categoryB = ApplicationCategory::factory()->create(['department_id' => $departmentB->id]);
    Application::factory()->create([
        'student_id' => $studentB->student->id,
        'department_id' => $departmentB->id,
        'category_id' => $categoryB->id,
        'subject' => 'Other department application',
    ]);

    Livewire::actingAs($admin)
        ->test(Index::class)
        ->assertSee('Own department application')
        ->assertDontSee('Other department application');

    expect($ownApplication)->not->toBeNull();
});

test('search matches by student name', function () {
    $department = Department::factory()->create();
    $admin = createAdminOfficer($department);
    $student = createStudentUser($department);
    $student->update(['name' => 'Ayesha Khan']);
    $category = ApplicationCategory::factory()->create(['department_id' => $department->id]);
    Application::factory()->create([
        'student_id' => $student->student->id,
        'department_id' => $department->id,
        'category_id' => $category->id,
        'subject' => 'Searchable subject',
    ]);

    Livewire::actingAs($admin)
        ->test(Index::class)
        ->set('search', 'Ayesha')
        ->assertSee('Searchable subject');
});

test('search matches by application number', function () {
    $department = Department::factory()->create();
    $admin = createAdminOfficer($department);
    $student = createStudentUser($department);
    $category = ApplicationCategory::factory()->create(['department_id' => $department->id]);
    $application = Application::createWithApplicationNumber([
        'student_id' => $student->student->id,
        'department_id' => $department->id,
        'category_id' => $category->id,
        'subject' => 'Searchable subject',
        'body' => 'Body text.',
        'semester_at_submission' => 3,
    ]);

    Livewire::actingAs($admin)
        ->test(Index::class)
        ->set('search', $application->application_no)
        ->assertSee('Searchable subject');
});

test('status filter narrows the results', function () {
    $department = Department::factory()->create();
    $admin = createAdminOfficer($department);
    $student = createStudentUser($department);
    $category = ApplicationCategory::factory()->create(['department_id' => $department->id]);

    Application::factory()->create([
        'student_id' => $student->student->id,
        'department_id' => $department->id,
        'category_id' => $category->id,
        'subject' => 'Submitted one',
        'status' => ApplicationStatus::Submitted,
    ]);
    Application::factory()->create([
        'student_id' => $student->student->id,
        'department_id' => $department->id,
        'category_id' => $category->id,
        'subject' => 'Resolved one',
        'status' => ApplicationStatus::Resolved,
    ]);

    Livewire::actingAs($admin)
        ->test(Index::class)
        ->set('status', 'resolved')
        ->assertSee('Resolved one')
        ->assertDontSee('Submitted one');
});
