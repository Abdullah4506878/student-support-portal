<?php

use App\Livewire\Auth\Register;
use App\Models\Department;
use Livewire\Livewire;

beforeEach(function () {
    createRoles();
    Department::factory()->create(['code' => 'SE']);
});

test('registration number is uppercased as it is typed', function () {
    Livewire::test(Register::class)
        ->set('registration_no', 'su92-bssem-f22-171')
        ->assertSet('registration_no', 'SU92-BSSEM-F22-171');
});

test('a field is validated when it changes and the error clears once fixed', function () {
    Livewire::test(Register::class)
        ->set('program', 'BSSE')
        ->set('registration_no', 'not-a-valid-format')
        ->assertHasErrors(['registration_no'])
        ->set('registration_no', 'SU92-BSSEM-F22-171')
        ->assertHasNoErrors(['registration_no']);
});

test('registration number must match the selected program', function () {
    Livewire::test(Register::class)
        ->set('program', 'BSDS')
        ->set('registration_no', 'SU92-BSSEM-F22-171')
        ->assertHasErrors(['registration_no']);
});

test('email must match the registration number', function () {
    Livewire::test(Register::class)
        ->set('registration_no', 'SU92-BSSEM-F22-171')
        ->set('email', 'someone-else@superior.edu.pk')
        ->assertHasErrors(['email'])
        ->set('email', 'su92-bssem-f22-171@superior.edu.pk')
        ->assertHasNoErrors(['email']);
});

test('the full registration form submits successfully', function () {
    Livewire::test(Register::class)
        ->set('name', 'Ayesha Khan')
        ->set('program', 'BSSE')
        ->set('registration_no', 'SU92-BSSEM-F22-171')
        ->set('email', 'su92-bssem-f22-171@superior.edu.pk')
        ->set('current_semester', '5')
        ->set('password', 'password')
        ->set('password_confirmation', 'password')
        ->call('register')
        ->assertHasNoErrors()
        ->assertRedirect(route('dashboard', absolute: false));

    $this->assertAuthenticated();
});
