<?php

use App\Enums\ApplicationEventType;
use App\Enums\ApplicationPriority;
use App\Enums\ApplicationStatus;
use App\Livewire\Student\Applications\Show;
use App\Models\Application;
use App\Models\ApplicationCategory;
use App\Models\ApplicationEvent;
use App\Models\InternalNote;
use Livewire\Livewire;

beforeEach(function () {
    createRoles();
});

function createApplicationForStudent($student): Application
{
    $category = ApplicationCategory::factory()->create(['department_id' => $student->department_id]);

    return Application::factory()->create([
        'student_id' => $student->student->id,
        'department_id' => $student->department_id,
        'category_id' => $category->id,
    ]);
}

test('a student can view their own application', function () {
    $student = createStudentUser();
    $application = createApplicationForStudent($student);

    Livewire::actingAs($student)
        ->test(Show::class, ['application' => $application])
        ->assertSee($application->subject)
        ->assertSee($application->application_no);
});

test('a student cannot view another student application', function () {
    $owner = createStudentUser();
    $attacker = createStudentUser();
    $application = createApplicationForStudent($owner);

    Livewire::actingAs($attacker)
        ->test(Show::class, ['application' => $application])
        ->assertForbidden();
});

test('only events visible to the student are shown on the timeline', function () {
    $student = createStudentUser();
    $application = createApplicationForStudent($student);

    ApplicationEvent::create([
        'application_id' => $application->id,
        'event_type' => ApplicationEventType::Submitted,
        'visible_to_student' => true,
    ]);
    ApplicationEvent::create([
        'application_id' => $application->id,
        'event_type' => ApplicationEventType::PriorityChanged,
        'visible_to_student' => false,
    ]);

    $response = Livewire::actingAs($student)->test(Show::class, ['application' => $application]);

    $response->assertSee(__('Application submitted.'));
    $response->assertDontSee('priority');
});

test('internal notes are never rendered on the student application page', function () {
    $student = createStudentUser();
    $application = createApplicationForStudent($student);

    $admin = createAdminOfficer($student->department);
    InternalNote::factory()->create([
        'application_id' => $application->id,
        'admin_id' => $admin->id,
        'body' => 'TOP-SECRET-INTERNAL-NOTE-TEXT',
    ]);

    Livewire::actingAs($student)
        ->test(Show::class, ['application' => $application])
        ->assertDontSee('TOP-SECRET-INTERNAL-NOTE-TEXT');
});

test('priority is never shown to the student', function () {
    $student = createStudentUser();
    $application = createApplicationForStudent($student);
    $application->update(['priority' => ApplicationPriority::Urgent]);

    Livewire::actingAs($student)
        ->test(Show::class, ['application' => $application])
        ->assertDontSee('Urgent')
        ->assertDontSee('urgent');
});

test('the resolution note is shown once the application is resolved', function () {
    $student = createStudentUser();
    $application = createApplicationForStudent($student);
    $application->update([
        'status' => ApplicationStatus::Resolved,
        'resolution_note' => 'Your fee challan has been corrected.',
    ]);

    Livewire::actingAs($student)
        ->test(Show::class, ['application' => $application])
        ->assertSee('Your fee challan has been corrected.');
});

test('the rejection reason is shown once the application is rejected', function () {
    $student = createStudentUser();
    $application = createApplicationForStudent($student);
    $application->update([
        'status' => ApplicationStatus::Rejected,
        'rejection_reason' => 'This issue is outside the department\'s scope.',
    ]);

    Livewire::actingAs($student)
        ->test(Show::class, ['application' => $application])
        ->assertSee('This issue is outside the department\'s scope.');
});
