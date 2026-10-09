<?php

namespace App\Actions\Fortify;

use App\Concerns\PasswordValidationRules;
use App\Enums\RoleName;
use App\Enums\UserStatus;
use App\Models\Department;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered student account.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        $input['registration_no'] = strtoupper((string) ($input['registration_no'] ?? ''));

        $pattern = config('students.registration_no_pattern');

        $validated = Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'ends_with:'.config('students.email_domain'),
                Rule::unique(User::class),
            ],
            'registration_no' => [
                'required',
                'string',
                'regex:'.$pattern,
                Rule::unique(Student::class),
            ],
            'current_semester' => ['required', 'integer', 'between:1,8'],
            'password' => $this->passwordRules(),
        ], [
            'email.ends_with' => __('The email must be a university email ending with :domain.', ['domain' => config('students.email_domain')]),
            'registration_no.regex' => __('The registration number format is invalid. Example: SU92-BSSEM-F22-171.'),
            'registration_no.unique' => __('This registration number is already registered.'),
        ])->validate();

        preg_match($pattern, $validated['registration_no'], $matches);
        $batch = $matches[1];

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
                'program' => config('students.default_program'),
                'current_semester' => $validated['current_semester'],
                'batch' => $batch,
            ]);

            return $user;
        });
    }
}
