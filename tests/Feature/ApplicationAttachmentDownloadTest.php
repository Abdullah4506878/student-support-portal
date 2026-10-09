<?php

use App\Models\Application;
use App\Models\ApplicationAttachment;
use App\Models\ApplicationCategory;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    createRoles();
    Storage::fake('local');
});

function createAttachmentFor($student): ApplicationAttachment
{
    $category = ApplicationCategory::factory()->create(['department_id' => $student->department_id]);
    $application = Application::factory()->create([
        'student_id' => $student->student->id,
        'department_id' => $student->department_id,
        'category_id' => $category->id,
    ]);

    $path = 'attachments/'.$application->id.'/random-name.pdf';
    Storage::disk('local')->put($path, 'file contents');

    return ApplicationAttachment::factory()->create([
        'application_id' => $application->id,
        'uploaded_by' => $student->id,
        'original_name' => 'original-document.pdf',
        'file_path' => $path,
    ]);
}

test('a student can download an attachment on their own application', function () {
    $student = createStudentUser();
    $attachment = createAttachmentFor($student);

    $response = $this->actingAs($student)->get(route('applications.attachments.show', $attachment));

    $response->assertOk();
    $response->assertHeader('content-disposition', 'attachment; filename=original-document.pdf');
});

test('a student cannot download another student attachment', function () {
    $owner = createStudentUser();
    $attacker = createStudentUser();
    $attachment = createAttachmentFor($owner);

    $this->actingAs($attacker)->get(route('applications.attachments.show', $attachment))
        ->assertForbidden();
});

test('an admin officer can download an attachment in their own department', function () {
    $student = createStudentUser();
    $admin = createAdminOfficer($student->department);
    $attachment = createAttachmentFor($student);

    $this->actingAs($admin)->get(route('applications.attachments.show', $attachment))
        ->assertOk();
});

test('an admin officer cannot download an attachment from another department', function () {
    $student = createStudentUser();
    $otherAdmin = createAdminOfficer();
    $attachment = createAttachmentFor($student);

    $this->actingAs($otherAdmin)->get(route('applications.attachments.show', $attachment))
        ->assertForbidden();
});
