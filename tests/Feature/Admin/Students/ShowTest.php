<?php

use App\Enums\UserStatus;
use App\Livewire\Admin\Students\Show;
use App\Models\Department;
use Livewire\Livewire;
use Spatie\Activitylog\Models\Activity;

beforeEach(function () {
    createRoles();
});

test('an admin officer can view a student profile with all their applications', function () {
    $department = Department::factory()->create();
    $admin = createAdminOfficer($department);
    $student = createStudentUser($department);
    $studentProfile = $student->student;

    Livewire::actingAs($admin)
        ->test(Show::class, ['student' => $studentProfile])
        ->assertSee($student->name)
        ->assertSee($studentProfile->registration_no)
        ->assertOk();
});

test('an admin officer cannot view a student profile in another department', function () {
    $departmentA = Department::factory()->create();
    $departmentB = Department::factory()->create();
    $admin = createAdminOfficer($departmentA);
    $student = createStudentUser($departmentB);
    $studentProfile = $student->student;

    Livewire::actingAs($admin)
        ->test(Show::class, ['student' => $studentProfile])
        ->assertForbidden();
});

test('an admin officer can suspend a student, which logs them out and takes effect immediately', function () {
    $department = Department::factory()->create();
    $admin = createAdminOfficer($department);
    $student = createStudentUser($department);
    $studentProfile = $student->student;

    Livewire::actingAs($admin)
        ->test(Show::class, ['student' => $studentProfile])
        ->call('suspend')
        ->assertHasNoErrors();

    expect($student->refresh()->status)->toBe(UserStatus::Suspended);

    $this->actingAs($student)
        ->get(route('student.dashboard'))
        ->assertRedirect(route('login'));

    $this->assertGuest();
});

test('an admin officer can reactivate a suspended student', function () {
    $department = Department::factory()->create();
    $admin = createAdminOfficer($department);
    $student = createStudentUser($department);
    $student->update(['status' => UserStatus::Suspended]);
    $studentProfile = $student->student;

    Livewire::actingAs($admin)
        ->test(Show::class, ['student' => $studentProfile])
        ->call('reactivate')
        ->assertHasNoErrors();

    expect($student->refresh()->status)->toBe(UserStatus::Active);
});

test('correcting a student\'s name is logged in the activity log and shows a success message', function () {
    $department = Department::factory()->create();
    $admin = createAdminOfficer($department);
    $student = createStudentUser($department);
    $student->update(['name' => 'Mohammad Ali']);
    $studentProfile = $student->student;

    Livewire::actingAs($admin)
        ->test(Show::class, ['student' => $studentProfile])
        ->set('name', 'Muhammad Ali')
        ->call('updateName')
        ->assertHasNoErrors();

    expect($student->refresh()->name)->toBe('Muhammad Ali');

    $activity = Activity::query()
        ->where('subject_type', $student->getMorphClass())
        ->where('subject_id', $student->id)
        ->where('event', 'updated')
        ->latest('id')
        ->first();

    expect($activity)->not->toBeNull();

    // The diff lives in attribute_changes, not properties, on this
    // package version — see UPGRADING.md for the v5 column rename.
    expect($activity->attribute_changes->get('old')['name'])->toBe('Mohammad Ali');
    expect($activity->attribute_changes->get('attributes')['name'])->toBe('Muhammad Ali');
});
