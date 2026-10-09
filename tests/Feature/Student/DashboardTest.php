<?php

beforeEach(function () {
    createRoles();
});

test('the dashboard shows the readable batch label, not the raw code', function () {
    $student = createStudentUser();
    $student->student->update(['batch' => 'F22']);

    $response = $this->actingAs($student)->get(route('student.dashboard'));

    $response->assertOk()
        ->assertSee('Fall 2022')
        ->assertDontSee('F22');
});
