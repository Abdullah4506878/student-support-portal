<?php

namespace App\Concerns;

use App\Models\Student;
use App\Models\User;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rule;

trait RegistrationValidationRules
{
    /**
     * @return array<int, ValidationRule|array<mixed>|string>
     */
    protected function nameRules(): array
    {
        return ['required', 'string', 'max:255'];
    }

    /**
     * @return array<int, ValidationRule|array<mixed>|string>
     */
    protected function programRules(): array
    {
        return ['required', 'string', Rule::in(array_keys(config('students.programs')))];
    }

    /**
     * @return array<int, ValidationRule|array<mixed>|string>
     */
    protected function currentSemesterRules(): array
    {
        return ['required', 'integer', 'between:1,8'];
    }

    /**
     * @return array<int, ValidationRule|Closure|array<mixed>|string>
     */
    protected function registrationNoRules(?string $program, ?int $ignoreStudentId = null): array
    {
        $unique = Rule::unique(Student::class, 'registration_no');

        if ($ignoreStudentId) {
            $unique = $unique->ignore($ignoreStudentId);
        }

        return [
            'required',
            'string',
            function (string $attribute, mixed $value, Closure $fail) use ($program) {
                $pattern = config('students.registration_no_pattern');

                if (! preg_match($pattern, strtoupper((string) $value), $matches)) {
                    $fail(__('Invalid registration number format.'));

                    return;
                }

                if ($program && $matches[1] !== $program) {
                    $fail(__("This registration number doesn't match the selected program."));
                }
            },
            $unique,
        ];
    }

    /**
     * @return array<int, ValidationRule|Closure|array<mixed>|string>
     */
    protected function emailRules(?string $registrationNo, ?int $ignoreUserId = null): array
    {
        $unique = Rule::unique(User::class);

        if ($ignoreUserId) {
            $unique = $unique->ignore($ignoreUserId);
        }

        $rules = [
            'required',
            'string',
            'email',
            'max:255',
            'ends_with:'.config('students.email_domain'),
            $unique,
        ];

        if (config('students.enforce_email_matches_registration_no') && $registrationNo) {
            $rules[] = function (string $attribute, mixed $value, Closure $fail) use ($registrationNo) {
                $expected = strtolower($registrationNo).config('students.email_domain');

                if (strtolower((string) $value) !== $expected) {
                    $fail(__('Your university email must match your registration number.'));
                }
            };
        }

        return $rules;
    }
}
