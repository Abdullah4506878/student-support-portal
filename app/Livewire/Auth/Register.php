<?php

namespace App\Livewire\Auth;

use App\Concerns\PasswordValidationRules;
use App\Concerns\RegistrationValidationRules;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Contracts\View\View;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Register')]
class Register extends Component
{
    use PasswordValidationRules, RegistrationValidationRules;

    public string $name = '';

    public string $registration_no = '';

    public string $email = '';

    public string $program = '';

    public string $current_semester = '';

    public string $password = '';

    public string $password_confirmation = '';

    /**
     * Registration number is always uppercased live, as the student types.
     */
    public function updatedRegistrationNo(string $value): void
    {
        $this->registration_no = strtoupper($value);
    }

    /**
     * Validate a single field: on blur the first time, then live once it
     * has an error (see the wire:model modifier chosen in the view).
     */
    public function updated(string $property): void
    {
        // The "confirmed" rule lives on the password field, so re-check it
        // once the confirmation changes too, clearing a stale mismatch.
        if ($property === 'password_confirmation' && $this->password !== '') {
            $this->validateOnly('password', ['password' => $this->passwordRules()]);

            return;
        }

        $rules = $this->rulesFor($property);

        if ($rules !== null) {
            $this->validateOnly($property, [$property => $rules]);
        }
    }

    /**
     * @return array<int, mixed>|null
     */
    private function rulesFor(string $property): ?array
    {
        return match ($property) {
            'name' => $this->nameRules(),
            'program' => $this->programRules(),
            'registration_no' => $this->registrationNoRules($this->program ?: null),
            'email' => $this->emailRules($this->registration_no ?: null),
            'current_semester' => $this->currentSemesterRules(),
            'password' => $this->passwordRules(),
            default => null,
        };
    }

    public function register(CreatesNewUsers $creator, StatefulGuard $guard): void
    {
        $user = $creator->create([
            'name' => $this->name,
            'registration_no' => $this->registration_no,
            'email' => $this->email,
            'program' => $this->program,
            'current_semester' => $this->current_semester,
            'password' => $this->password,
            'password_confirmation' => $this->password_confirmation,
        ]);

        event(new Registered($user));

        // SessionGuard::login() already regenerates the session ID.
        $guard->login($user);

        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }

    public function render(): View
    {
        return view('livewire.auth.register-form');
    }
}
