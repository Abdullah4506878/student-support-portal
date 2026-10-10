<?php

use App\Models\Announcement;

beforeEach(function () {
    createRoles();
});

test('the dashboard shows the readable batch label, not the raw code', function () {
    $student = createStudentUser();
    $student->student->update(['batch' => 'F22', 'registration_no' => 'SU92-BSSEM-F22-900']);

    $response = $this->actingAs($student)->get(route('student.dashboard'));

    $response->assertOk()->assertSee('Fall 2022');

    // The raw batch code must not appear on its own, outside the registration number.
    $withoutRegistrationNo = str_replace('SU92-BSSEM-F22-900', '', $response->getContent());
    expect($withoutRegistrationNo)->not->toContain('F22');
});

test('the dashboard shows the latest 3 visible announcements for the department, newest first', function () {
    $student = createStudentUser();

    Announcement::factory()->create([
        'department_id' => $student->department_id,
        'title' => 'Oldest visible',
        'is_active' => true,
        'publish_at' => now()->subDays(5),
    ]);
    Announcement::factory()->create([
        'department_id' => $student->department_id,
        'title' => 'Newest visible',
        'is_active' => true,
        'publish_at' => now()->subDay(),
    ]);
    Announcement::factory()->create([
        'department_id' => $student->department_id,
        'title' => 'Hidden draft',
        'is_active' => false,
    ]);

    $response = $this->actingAs($student)->get(route('student.dashboard'));

    $response->assertOk()
        ->assertSeeInOrder(['Newest visible', 'Oldest visible'])
        ->assertDontSee('Hidden draft');
});
