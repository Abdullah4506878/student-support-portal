<?php

use App\Livewire\Notifications\Index;
use App\Notifications\ApplicationClosed;
use Livewire\Livewire;

beforeEach(function () {
    createRoles();
});

test('a student only sees their own notifications', function () {
    $owner = createStudentUser();
    $other = createStudentUser();
    $application = createApplicationInDepartment($owner->department, $owner);

    $owner->notify(new ApplicationClosed($application));
    $other->notify(new ApplicationClosed($application));

    expect($owner->notifications()->count())->toBe(1);

    $component = Livewire::actingAs($owner)->test(Index::class);

    expect($component->get('notifications')->total())->toBe(1);
});

test('a user cannot mark another user\'s notification as read', function () {
    $owner = createStudentUser();
    $attacker = createStudentUser();
    $application = createApplicationInDepartment($owner->department, $owner);

    $owner->notify(new ApplicationClosed($application));
    $notification = $owner->notifications()->firstOrFail();

    Livewire::actingAs($attacker)
        ->test(Index::class)
        ->call('markAsRead', $notification->id)
        ->assertStatus(404);

    expect($notification->refresh()->read_at)->toBeNull();
});

test('a user cannot open (and thereby mark as read) another user\'s notification', function () {
    $owner = createStudentUser();
    $attacker = createStudentUser();
    $application = createApplicationInDepartment($owner->department, $owner);

    $owner->notify(new ApplicationClosed($application));
    $notification = $owner->notifications()->firstOrFail();

    Livewire::actingAs($attacker)
        ->test(Index::class)
        ->call('open', $notification->id)
        ->assertStatus(404);

    expect($notification->refresh()->read_at)->toBeNull();
});

test('a student can mark a single notification as read', function () {
    $student = createStudentUser();
    $application = createApplicationInDepartment($student->department, $student);
    $student->notify(new ApplicationClosed($application));
    $notification = $student->notifications()->firstOrFail();

    Livewire::actingAs($student)
        ->test(Index::class)
        ->call('markAsRead', $notification->id)
        ->assertHasNoErrors();

    expect($notification->refresh()->read_at)->not->toBeNull();
});

test('a student can mark all notifications as read at once', function () {
    $student = createStudentUser();
    $application = createApplicationInDepartment($student->department, $student);
    $student->notify(new ApplicationClosed($application));
    $student->notify(new ApplicationClosed($application));

    expect($student->unreadNotifications()->count())->toBe(2);

    Livewire::actingAs($student)
        ->test(Index::class)
        ->call('markAllAsRead');

    expect($student->unreadNotifications()->count())->toBe(0);
});

test('opening a notification marks it read and redirects to its url', function () {
    $student = createStudentUser();
    $application = createApplicationInDepartment($student->department, $student);
    $student->notify(new ApplicationClosed($application));
    $notification = $student->notifications()->firstOrFail();

    Livewire::actingAs($student)
        ->test(Index::class)
        ->call('open', $notification->id)
        ->assertRedirect(route('student.applications.show', $application));

    expect($notification->refresh()->read_at)->not->toBeNull();
});
