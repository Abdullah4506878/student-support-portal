<?php

use App\Models\Announcement;
use App\Models\Department;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    createRoles();
    Storage::fake('local');
});

function createAnnouncementWithFiles(Department $department): Announcement
{
    $image = UploadedFile::fake()->image('poster.jpg');
    $attachment = UploadedFile::fake()->create('form.pdf', 200, 'application/pdf');

    return Announcement::factory()->create([
        'department_id' => $department->id,
        'is_active' => true,
        'publish_at' => now()->subDay(),
        'image_path' => $image->store('announcements/test', 'local'),
        'attachment_path' => $attachment->store('announcements/test', 'local'),
        'attachment_original_name' => 'form.pdf',
    ]);
}

test('a student of the same department can view the image and download the attachment', function () {
    $department = Department::factory()->create();
    $student = createStudentUser($department);
    $announcement = createAnnouncementWithFiles($department);

    $this->actingAs($student)->get(route('announcements.image.show', $announcement))->assertOk();
    $this->actingAs($student)->get(route('announcements.attachment.show', $announcement))->assertOk();
});

test('a student of another department cannot view the image or the attachment', function () {
    $departmentA = Department::factory()->create();
    $departmentB = Department::factory()->create();
    $student = createStudentUser($departmentA);
    $announcement = createAnnouncementWithFiles($departmentB);

    $this->actingAs($student)->get(route('announcements.image.show', $announcement))->assertForbidden();
    $this->actingAs($student)->get(route('announcements.attachment.show', $announcement))->assertForbidden();
});

test('a student cannot view the files of an inactive (draft) announcement', function () {
    $department = Department::factory()->create();
    $student = createStudentUser($department);
    $announcement = createAnnouncementWithFiles($department);
    $announcement->update(['is_active' => false]);

    $this->actingAs($student)->get(route('announcements.image.show', $announcement))->assertForbidden();
});

test('an admin officer of the same department can view the files', function () {
    $department = Department::factory()->create();
    $admin = createAdminOfficer($department);
    $announcement = createAnnouncementWithFiles($department);

    $this->actingAs($admin)->get(route('announcements.image.show', $announcement))->assertOk();
    $this->actingAs($admin)->get(route('announcements.attachment.show', $announcement))->assertOk();
});

test('an admin officer of another department cannot view the files', function () {
    $departmentA = Department::factory()->create();
    $departmentB = Department::factory()->create();
    $admin = createAdminOfficer($departmentA);
    $announcement = createAnnouncementWithFiles($departmentB);

    $this->actingAs($admin)->get(route('announcements.image.show', $announcement))->assertForbidden();
});
