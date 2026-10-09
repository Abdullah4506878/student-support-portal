<?php

namespace App\Actions\Fortify;

use App\Concerns\PasswordValidationRules;
use App\Concerns\RegistrationValidationRules;
use App\Enums\RoleName;
use App\Enums\UserStatus;
use App\Models\Department;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules, RegistrationValidationRules;

    /**
     * Validate and create a newly registered student account.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        $input['registration_no'] = strtoupper((string) ($input['registration_no'] ?? ''));
        $program = $input['program'] ?? null;

        $validated = Validator::make($input, [
            'name' => $this->nameRules(),
            'program' => $this->programRules(),
            'registration_no' => $this->registrationNoRules($program),
            'email' => $this->emailRules($input['registration_no']),
            'current_semester' => $this->currentSemesterRules(),
            'password' => $this->passwordRules(),
        ], $this->registrationMessages())->validate();

        $pattern = config('students.registration_no_pattern');
        preg_match($pattern, $validated['registration_no'], $matches);
        $batch = $matches[2];

        $department = Department::query()->where('code', 'SE')->firstOrFail();

        return DB::transaction(function () use ($validated, $batch, $department) {
            $user = User::create([
                'name' => $validated['name'],
                'email' => strtolower($validated['email']),
                'password' => $validated['password'],
                'department_id' => $department->id,
                'status' => UserStatus::PendingVerification,
            ]);

            $user->assignRole(RoleName::Student->value);

            Student::create([
                'user_id' => $user->id,
                'registration_no' => $validated['registration_no'],
                'program' => config('students.programs')[$validated['program']],
                'current_semester' => $validated['current_semester'],
                'batch' => $batch,
            ]);

            return $user;
        });
    }
}
