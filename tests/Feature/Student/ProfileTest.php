<?php

use App\Livewire\Student\Profile;
use Livewire\Livewire;

beforeEach(function () {
    createRoles();
});

test('a student can view their profile page', function () {
    $student = createStudentUser();

    $response = $this->actingAs($student)->get(route('student.profile'));

    $response->assertOk()
        ->assertSee($student->student->registration_no)
        ->assertSee($student->email);
});

test('a student can update their current semester', function () {
    $student = createStudentUser();
    $student->student->update(['current_semester' => 3]);

    Livewire::actingAs($student)
        ->test(Profile::class)
        ->set('current_semester', 7)
        ->call('updateSemester')
        ->assertHasNoErrors();

    expect($student->student->refresh()->current_semester)->toBe(7);
});

test('the current semester must be between 1 and 8', function () {
    $student = createStudentUser();

    Livewire::actingAs($student)
        ->test(Profile::class)
        ->set('current_semester', 9)
        ->call('updateSemester')
        ->assertHasErrors(['current_semester']);
});

test('batch is displayed as a readable label', function () {
    $student = createStudentUser();
    $student->student->update(['batch' => 'F22']);

    expect($student->student->batch_label)->toBe('Fall 2022');

    $student->student->update(['batch' => 'S24']);

    expect($student->student->refresh()->batch_label)->toBe('Spring 2024');
});
