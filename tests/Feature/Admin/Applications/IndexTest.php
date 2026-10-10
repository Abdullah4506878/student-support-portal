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

test('the status filter can be reset back to showing everything', function () {
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
        ->assertDontSee('Submitted one')
        ->set('status', '')
        ->assertSee('Submitted one')
        ->assertSee('Resolved one');
});

test('clearFilters resets search, every dropdown and the date range', function () {
    $department = Department::factory()->create();
    $admin = createAdminOfficer($department);

    $component = Livewire::actingAs($admin)
        ->test(Index::class)
        ->set('search', 'something')
        ->set('status', 'resolved')
        ->set('priority', 'urgent')
        ->set('category_id', '1')
        ->set('semester', '3')
        ->set('date_from', '2026-01-01')
        ->set('date_to', '2026-01-31');

    expect($component->get('hasActiveFilters'))->toBeTrue();

    $component->call('clearFilters');

    expect($component->get('search'))->toBe('');
    expect($component->get('status'))->toBe('');
    expect($component->get('priority'))->toBe('');
    expect($component->get('category_id'))->toBe('');
    expect($component->get('semester'))->toBe('');
    expect($component->get('date_from'))->toBe('');
    expect($component->get('date_to'))->toBe('');
});

test('visiting the index with a search query string (as the top-bar search does) filters the list', function () {
    $department = Department::factory()->create();
    $admin = createAdminOfficer($department);
    $student = createStudentUser($department);
    $student->update(['name' => 'Zarmeen Abbas']);
    $category = ApplicationCategory::factory()->create(['department_id' => $department->id]);
    Application::factory()->create([
        'student_id' => $student->student->id,
        'department_id' => $department->id,
        'category_id' => $category->id,
        'subject' => 'Findable via top bar search',
    ]);

    $this->actingAs($admin)
        ->get(route('admin.applications.index', ['search' => 'Zarmeen']))
        ->assertOk()
        ->assertSee('Findable via top bar search');
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
