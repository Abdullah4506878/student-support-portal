<?php

use App\Enums\UserStatus;

beforeEach(function () {
    createRoles();
});

test('a suspended user is logged out on their next request and sees the suspension message', function () {
    $user = createStudentUser();

    $this->actingAs($user)->get(route('student.dashboard'))->assertOk();

    $user->update(['status' => UserStatus::Suspended]);

    $response = $this->get(route('student.dashboard'));

    $response->assertRedirect(route('login'));
    $response->assertSessionHas('status', 'Your account has been suspended. Contact the SE Department Admin Office.');
    $this->assertGuest();
});

test('an active user is unaffected by the suspension check', function () {
    $user = createStudentUser();

    $this->actingAs($user)->get(route('student.dashboard'))->assertOk();
    $this->assertAuthenticatedAs($user);
});
