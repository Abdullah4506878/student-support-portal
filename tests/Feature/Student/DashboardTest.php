<?php

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
