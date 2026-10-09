<?php

use App\Enums\ApplicationStatus;
use App\Livewire\Student\Applications\Index;
use App\Models\Application;
use App\Models\ApplicationCategory;
use Livewire\Livewire;

beforeEach(function () {
    createRoles();
});

test('a student only sees their own applications', function () {
    $student = createStudentUser();
    $other = createStudentUser();
    $category = ApplicationCategory::factory()->create(['department_id' => $student->department_id]);

    $mine = Application::factory()->create([
        'student_id' => $student->student->id,
        'department_id' => $student->department_id,
        'category_id' => $category->id,
        'subject' => 'My own application',
    ]);
    Application::factory()->create([
        'student_id' => $other->student->id,
        'department_id' => $other->department_id,
        'category_id' => ApplicationCategory::factory()->create(['department_id' => $other->department_id])->id,
        'subject' => 'Someone else\'s application',
    ]);

    Livewire::actingAs($student)
        ->test(Index::class)
        ->assertSee('My own application')
        ->assertDontSee("Someone else's application");
});

test('a student can search by application number or subject', function () {
    $student = createStudentUser();
    $category = ApplicationCategory::factory()->create(['department_id' => $student->department_id]);

    Application::factory()->create([
        'student_id' => $student->student->id,
        'department_id' => $student->department_id,
        'category_id' => $category->id,
        'subject' => 'Fee challan is wrong',
    ]);
    Application::factory()->create([
        'student_id' => $student->student->id,
        'department_id' => $student->department_id,
        'category_id' => $category->id,
        'subject' => 'Exam result missing',
    ]);

    Livewire::actingAs($student)
        ->test(Index::class)
        ->set('search', 'Fee challan')
        ->assertSee('Fee challan is wrong')
        ->assertDontSee('Exam result missing');
});

test('a student can filter their applications by status', function () {
    $student = createStudentUser();
    $category = ApplicationCategory::factory()->create(['department_id' => $student->department_id]);

    Application::factory()->create([
        'student_id' => $student->student->id,
        'department_id' => $student->department_id,
        'category_id' => $category->id,
        'subject' => 'Resolved one',
        'status' => ApplicationStatus::Resolved,
    ]);
    Application::factory()->create([
        'student_id' => $student->student->id,
        'department_id' => $student->department_id,
        'category_id' => $category->id,
        'subject' => 'Submitted one',
        'status' => ApplicationStatus::Submitted,
    ]);

    Livewire::actingAs($student)
        ->test(Index::class)
        ->set('status', ApplicationStatus::Resolved->value)
        ->assertSee('Resolved one')
        ->assertDontSee('Submitted one');
});
