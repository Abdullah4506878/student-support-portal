<?php

use App\Enums\ApplicationEventType;
use App\Enums\ApplicationStatus;
use App\Enums\MessageType;
use App\Livewire\Student\Applications\Show;
use App\Models\ApplicationEvent;
use App\Models\ApplicationMessage;
use App\Models\InternalNote;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

beforeEach(function () {
    createRoles();
    Storage::fake('local');
});

function createOpenRequest($application, ?int $senderId = null): ApplicationMessage
{
    $application->update(['status' => ApplicationStatus::InfoRequired]);

    return ApplicationMessage::create([
        'application_id' => $application->id,
        'sender_id' => $senderId,
        'type' => MessageType::InfoRequest,
        'body' => 'Please upload your fee challan.',
    ]);
}

test('a student can respond to an open request with text only', function () {
    $student = createStudentUser();
    $application = createApplicationForStudent($student);
    $admin = createAdminOfficer($student->department);
    $request = createOpenRequest($application, $admin->id);

    Livewire::actingAs($student)
        ->test(Show::class, ['application' => $application])
        ->set('response_body', 'Here is the challan number: 12345.')
        ->call('respond')
        ->assertHasNoErrors();

    expect($request->refresh()->responded_at)->not->toBeNull();
    expect($application->refresh()->status)->toBe(ApplicationStatus::UnderReview);

    $response = ApplicationMessage::query()->where('type', MessageType::StudentResponse->value)->firstOrFail();
    expect($response->sender_id)->toBe($student->id);
    expect($response->parent_id)->toBe($request->id);
    expect($response->body)->toBe('Here is the challan number: 12345.');
});

test('a student can respond to an open request with a file and no text', function () {
    $student = createStudentUser();
    $application = createApplicationForStudent($student);
    $admin = createAdminOfficer($student->department);
    createOpenRequest($application, $admin->id);

    $file = UploadedFile::fake()->create('challan.pdf', 200, 'application/pdf');

    Livewire::actingAs($student)
        ->test(Show::class, ['application' => $application])
        ->set('response_attachments', [$file])
        ->call('respond')
        ->assertHasNoErrors();

    $response = ApplicationMessage::query()->where('type', MessageType::StudentResponse->value)->firstOrFail();
    $attachment = $response->attachments()->firstOrFail();
    expect($attachment->original_name)->toBe('challan.pdf');
    Storage::disk('local')->assertExists($attachment->file_path);
});

test('a response needs at least a body or a file', function () {
    $student = createStudentUser();
    $application = createApplicationForStudent($student);
    $admin = createAdminOfficer($student->department);
    createOpenRequest($application, $admin->id);

    Livewire::actingAs($student)
        ->test(Show::class, ['application' => $application])
        ->call('respond')
        ->assertHasErrors(['response_body']);
});

test('a response is capped at 1000 characters', function () {
    $student = createStudentUser();
    $application = createApplicationForStudent($student);
    $admin = createAdminOfficer($student->department);
    createOpenRequest($application, $admin->id);

    Livewire::actingAs($student)
        ->test(Show::class, ['application' => $application])
        ->set('response_body', str_repeat('a', 1001))
        ->call('respond')
        ->assertHasErrors(['response_body' => 'max']);
});

test('a student cannot respond when no request is open', function () {
    $student = createStudentUser();
    $application = createApplicationForStudent($student);

    Livewire::actingAs($student)
        ->test(Show::class, ['application' => $application])
        ->set('response_body', 'Here you go.')
        ->call('respond')
        ->assertForbidden();
});

test('a student cannot respond to another student\'s request', function () {
    $owner = createStudentUser();
    $attacker = createStudentUser();
    $application = createApplicationForStudent($owner);
    $admin = createAdminOfficer($owner->department);
    createOpenRequest($application, $admin->id);

    // Viewing someone else's application is already forbidden at mount()
    // time, before any response could ever be attempted.
    Livewire::actingAs($attacker)
        ->test(Show::class, ['application' => $application])
        ->assertForbidden();
});

test('a student cannot respond to a request that was auto-cancelled', function () {
    $student = createStudentUser();
    $application = createApplicationForStudent($student);
    $admin = createAdminOfficer($student->department);
    $request = createOpenRequest($application, $admin->id);
    $request->update(['cancelled_at' => now()]);

    Livewire::actingAs($student)
        ->test(Show::class, ['application' => $application])
        ->set('response_body', 'Too late?')
        ->call('respond')
        ->assertForbidden();
});

test('opening the application marks admin messages as read', function () {
    $student = createStudentUser();
    $application = createApplicationForStudent($student);
    $admin = createAdminOfficer($student->department);

    $message = ApplicationMessage::create([
        'application_id' => $application->id,
        'sender_id' => $admin->id,
        'type' => MessageType::Message,
        'body' => 'Please check your email.',
    ]);

    expect($message->read_at)->toBeNull();

    Livewire::actingAs($student)->test(Show::class, ['application' => $application]);

    expect($message->refresh()->read_at)->not->toBeNull();
});

test('internal notes and priority changes never appear in the merged timeline', function () {
    $student = createStudentUser();
    $application = createApplicationForStudent($student);
    $admin = createAdminOfficer($student->department);

    ApplicationMessage::create([
        'application_id' => $application->id,
        'sender_id' => $admin->id,
        'type' => MessageType::Message,
        'body' => 'VISIBLE-MESSAGE-TEXT',
    ]);

    InternalNote::factory()->create([
        'application_id' => $application->id,
        'admin_id' => $admin->id,
        'body' => 'HIDDEN-INTERNAL-NOTE',
    ]);

    ApplicationEvent::create([
        'application_id' => $application->id,
        'event_type' => ApplicationEventType::PriorityChanged,
        'from_value' => 'normal',
        'to_value' => 'urgent',
        'visible_to_student' => false,
    ]);

    Livewire::actingAs($student)
        ->test(Show::class, ['application' => $application])
        ->assertSee('VISIBLE-MESSAGE-TEXT')
        ->assertDontSee('HIDDEN-INTERNAL-NOTE')
        ->assertDontSee('urgent');
});
