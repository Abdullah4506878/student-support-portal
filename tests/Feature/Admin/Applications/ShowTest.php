<?php

use App\Enums\ApplicationEventType;
use App\Enums\ApplicationPriority;
use App\Enums\ApplicationStatus;
use App\Livewire\Admin\Applications\Show;
use App\Models\Application;
use App\Models\ApplicationCategory;
use App\Models\Department;
use App\Models\InternalNote;
use Livewire\Livewire;

beforeEach(function () {
    createRoles();
});

function createApplicationInDepartment(Department $department, $student): Application
{
    $category = ApplicationCategory::factory()->create(['department_id' => $department->id]);

    return Application::factory()->create([
        'student_id' => $student->student->id,
        'department_id' => $department->id,
        'category_id' => $category->id,
    ]);
}

test('an admin officer can view an application in their own department', function () {
    $department = Department::factory()->create();
    $admin = createAdminOfficer($department);
    $student = createStudentUser($department);
    $application = createApplicationInDepartment($department, $student);

    Livewire::actingAs($admin)
        ->test(Show::class, ['application' => $application])
        ->assertSee($application->subject)
        ->assertOk();
});

test('an admin officer cannot view an application in another department', function () {
    $departmentA = Department::factory()->create();
    $departmentB = Department::factory()->create();
    $admin = createAdminOfficer($departmentA);
    $student = createStudentUser($departmentB);
    $application = createApplicationInDepartment($departmentB, $student);

    Livewire::actingAs($admin)
        ->test(Show::class, ['application' => $application])
        ->assertForbidden();
});

test('changing priority records a priority_changed event that is never visible to the student', function () {
    $department = Department::factory()->create();
    $admin = createAdminOfficer($department);
    $student = createStudentUser($department);
    $application = createApplicationInDepartment($department, $student);

    Livewire::actingAs($admin)
        ->test(Show::class, ['application' => $application])
        ->set('priority_input', ApplicationPriority::Urgent->value)
        ->call('updatePriority')
        ->assertHasNoErrors();

    expect($application->refresh()->priority)->toBe(ApplicationPriority::Urgent);

    $event = $application->events()->latest('created_at')->first();
    expect($event->event_type)->toBe(ApplicationEventType::PriorityChanged);
    expect($event->visible_to_student)->toBeFalse();
    expect($event->from_value)->toBe('normal');
    expect($event->to_value)->toBe('urgent');
});

test('changing status records a status_changed event that is visible to the student', function () {
    $department = Department::factory()->create();
    $admin = createAdminOfficer($department);
    $student = createStudentUser($department);
    $application = createApplicationInDepartment($department, $student);

    Livewire::actingAs($admin)
        ->test(Show::class, ['application' => $application])
        ->set('status_input', 'under_review')
        ->call('updateStatus')
        ->assertHasNoErrors();

    expect($application->refresh()->status)->toBe(ApplicationStatus::UnderReview);

    $event = $application->events()->latest('created_at')->first();
    expect($event->event_type)->toBe(ApplicationEventType::StatusChanged);
    expect($event->visible_to_student)->toBeTrue();
});

test('resolving an application requires a resolution note', function () {
    $department = Department::factory()->create();
    $admin = createAdminOfficer($department);
    $student = createStudentUser($department);
    $application = createApplicationInDepartment($department, $student);

    Livewire::actingAs($admin)
        ->test(Show::class, ['application' => $application])
        ->set('resolution_note', '')
        ->call('resolve')
        ->assertHasErrors(['resolution_note' => 'required']);

    expect($application->refresh()->status)->toBe(ApplicationStatus::Submitted);
});

test('resolving an application sets resolved_at and creates a visible event', function () {
    $department = Department::factory()->create();
    $admin = createAdminOfficer($department);
    $student = createStudentUser($department);
    $application = createApplicationInDepartment($department, $student);

    Livewire::actingAs($admin)
        ->test(Show::class, ['application' => $application])
        ->set('resolution_note', 'Fee challan corrected.')
        ->call('resolve')
        ->assertHasNoErrors();

    $application->refresh();
    expect($application->status)->toBe(ApplicationStatus::Resolved);
    expect($application->resolution_note)->toBe('Fee challan corrected.');
    expect($application->resolved_at)->not->toBeNull();

    $event = $application->events()->latest('created_at')->first();
    expect($event->event_type)->toBe(ApplicationEventType::Resolved);
    expect($event->visible_to_student)->toBeTrue();
});

test('rejecting an application requires a reason', function () {
    $department = Department::factory()->create();
    $admin = createAdminOfficer($department);
    $student = createStudentUser($department);
    $application = createApplicationInDepartment($department, $student);

    Livewire::actingAs($admin)
        ->test(Show::class, ['application' => $application])
        ->set('rejection_reason', '')
        ->call('reject')
        ->assertHasErrors(['rejection_reason' => 'required']);

    expect($application->refresh()->status)->toBe(ApplicationStatus::Submitted);
});

test('rejecting an application with a reason records a visible event', function () {
    $department = Department::factory()->create();
    $admin = createAdminOfficer($department);
    $student = createStudentUser($department);
    $application = createApplicationInDepartment($department, $student);

    Livewire::actingAs($admin)
        ->test(Show::class, ['application' => $application])
        ->set('rejection_reason', 'Outside department scope.')
        ->call('reject')
        ->assertHasNoErrors();

    $application->refresh();
    expect($application->status)->toBe(ApplicationStatus::Rejected);
    expect($application->rejection_reason)->toBe('Outside department scope.');

    $event = $application->events()->latest('created_at')->first();
    expect($event->event_type)->toBe(ApplicationEventType::Rejected);
    expect($event->visible_to_student)->toBeTrue();
});

test('close is allowed even from resolved', function () {
    $department = Department::factory()->create();
    $admin = createAdminOfficer($department);
    $student = createStudentUser($department);
    $application = createApplicationInDepartment($department, $student);
    $application->update(['status' => ApplicationStatus::Resolved, 'resolved_at' => now()]);

    Livewire::actingAs($admin)
        ->test(Show::class, ['application' => $application])
        ->call('close')
        ->assertHasNoErrors();

    $application->refresh();
    expect($application->status)->toBe(ApplicationStatus::Closed);
    expect($application->closed_at)->not->toBeNull();
});

test('closed applications can no longer be changed', function () {
    $department = Department::factory()->create();
    $admin = createAdminOfficer($department);
    $student = createStudentUser($department);
    $application = createApplicationInDepartment($department, $student);
    $application->update(['status' => ApplicationStatus::Closed, 'closed_at' => now()]);

    Livewire::actingAs($admin)
        ->test(Show::class, ['application' => $application])
        ->set('status_input', 'under_review')
        ->call('updateStatus')
        ->assertForbidden();
});

test('rejected applications can no longer be changed', function () {
    $department = Department::factory()->create();
    $admin = createAdminOfficer($department);
    $student = createStudentUser($department);
    $application = createApplicationInDepartment($department, $student);
    $application->update(['status' => ApplicationStatus::Rejected, 'rejection_reason' => 'Already rejected.']);

    Livewire::actingAs($admin)
        ->test(Show::class, ['application' => $application])
        ->call('close')
        ->assertForbidden();
});

test('an admin officer can add and later edit their own internal note', function () {
    $department = Department::factory()->create();
    $admin = createAdminOfficer($department);
    $student = createStudentUser($department);
    $application = createApplicationInDepartment($department, $student);

    $component = Livewire::actingAs($admin)
        ->test(Show::class, ['application' => $application])
        ->set('new_note', 'Called the student to confirm details.')
        ->call('addNote')
        ->assertHasNoErrors();

    $note = InternalNote::query()->where('application_id', $application->id)->firstOrFail();
    expect($note->admin_id)->toBe($admin->id);
    expect($note->body)->toBe('Called the student to confirm details.');

    $component->call('startEditingNote', $note->id)
        ->set('editing_note_body', 'Updated note body.')
        ->call('saveNote')
        ->assertHasNoErrors();

    expect($note->refresh()->body)->toBe('Updated note body.');
});

test('an admin officer cannot edit another admin officer\'s internal note', function () {
    $department = Department::factory()->create();
    $author = createAdminOfficer($department);
    $otherAdmin = createAdminOfficer($department);
    $student = createStudentUser($department);
    $application = createApplicationInDepartment($department, $student);

    $note = InternalNote::factory()->create([
        'application_id' => $application->id,
        'admin_id' => $author->id,
    ]);

    Livewire::actingAs($otherAdmin)
        ->test(Show::class, ['application' => $application])
        ->call('startEditingNote', $note->id)
        ->assertForbidden();
});

test('internal notes are visible to the admin officer who manages the application', function () {
    $department = Department::factory()->create();
    $admin = createAdminOfficer($department);
    $student = createStudentUser($department);
    $application = createApplicationInDepartment($department, $student);

    InternalNote::factory()->create([
        'application_id' => $application->id,
        'admin_id' => $admin->id,
        'body' => 'Called the student to confirm the fee amount.',
    ]);

    Livewire::actingAs($admin)
        ->test(Show::class, ['application' => $application])
        ->assertSee('Called the student to confirm the fee amount.');
});
