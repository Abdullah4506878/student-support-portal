<?php

use App\Livewire\Student\Announcements\Index;
use App\Models\Announcement;
use App\Models\Department;
use Livewire\Livewire;

beforeEach(function () {
    createRoles();
});

test('a student sees active, published, not-expired announcements for their own department', function () {
    $department = Department::factory()->create();
    $student = createStudentUser($department);

    Announcement::factory()->create([
        'department_id' => $department->id,
        'title' => 'Visible announcement',
        'is_active' => true,
        'publish_at' => now()->subDay(),
        'expires_at' => null,
    ]);

    Livewire::actingAs($student)
        ->test(Index::class)
        ->assertSee('Visible announcement');
});

test('a scheduled announcement is hidden from students until its publish date', function () {
    $department = Department::factory()->create();
    $student = createStudentUser($department);

    Announcement::factory()->create([
        'department_id' => $department->id,
        'title' => 'Scheduled announcement',
        'is_active' => true,
        'publish_at' => now()->addDay(),
    ]);

    Livewire::actingAs($student)
        ->test(Index::class)
        ->assertDontSee('Scheduled announcement');
});

test('an expired announcement is hidden from students', function () {
    $department = Department::factory()->create();
    $student = createStudentUser($department);

    Announcement::factory()->create([
        'department_id' => $department->id,
        'title' => 'Expired announcement',
        'is_active' => true,
        'publish_at' => now()->subWeek(),
        'expires_at' => now()->subDay(),
    ]);

    Livewire::actingAs($student)
        ->test(Index::class)
        ->assertDontSee('Expired announcement');
});

test('an inactive announcement is hidden from students', function () {
    $department = Department::factory()->create();
    $student = createStudentUser($department);

    Announcement::factory()->create([
        'department_id' => $department->id,
        'title' => 'Inactive announcement',
        'is_active' => false,
    ]);

    Livewire::actingAs($student)
        ->test(Index::class)
        ->assertDontSee('Inactive announcement');
});

test('a student of another department cannot see the announcement', function () {
    $departmentA = Department::factory()->create();
    $departmentB = Department::factory()->create();
    $student = createStudentUser($departmentA);

    Announcement::factory()->create([
        'department_id' => $departmentB->id,
        'title' => 'Other department announcement',
        'is_active' => true,
        'publish_at' => now()->subDay(),
    ]);

    Livewire::actingAs($student)
        ->test(Index::class)
        ->assertDontSee('Other department announcement');
});

test('an announcement published within the last 3 days shows a new badge', function () {
    $department = Department::factory()->create();
    $student = createStudentUser($department);

    Announcement::factory()->create([
        'department_id' => $department->id,
        'title' => 'Fresh announcement',
        'is_active' => true,
        'publish_at' => now()->subDay(),
    ]);

    Announcement::factory()->create([
        'department_id' => $department->id,
        'title' => 'Old announcement',
        'is_active' => true,
        'publish_at' => now()->subDays(10),
    ]);

    $response = Livewire::actingAs($student)->test(Index::class);

    $response->assertSeeInOrder(['Fresh announcement', 'New']);
    $response->assertSee('Old announcement');
});
