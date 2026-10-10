<?php

namespace Database\Seeders;

use App\Enums\ApplicationStatus;
use App\Enums\RoleName;
use App\Enums\UserStatus;
use App\Models\Application;
use App\Models\ApplicationCategory;
use App\Models\Department;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Sample students and applications for UI testing.
 *
 * Not called from DatabaseSeeder — run explicitly with:
 *   php artisan db:seed --class=DemoSeeder
 *
 * To refresh an already-seeded database's demo rows in place (without
 * creating new ones or touching anything else), use `php artisan demo:refresh`.
 */
class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $department = Department::query()->where('code', 'SE')->firstOrFail();
        $categories = ApplicationCategory::query()->where('department_id', $department->id)->get()->keyBy('name');

        foreach (self::demoStudents() as $def) {
            $registrationNo = self::registrationNo($def);

            $user = User::query()->create([
                'name' => $def['name'],
                'email' => strtolower($registrationNo).config('students.email_domain'),
                'password' => config('seeding.default_password'),
                'department_id' => $department->id,
                'status' => UserStatus::Active,
                'email_verified_at' => now(),
            ]);
            $user->syncRoles([RoleName::Student->value]);

            $student = Student::query()->create([
                'user_id' => $user->id,
                'registration_no' => $registrationNo,
                'program' => config('students.programs')[$def['program_code']],
                'current_semester' => $def['current_semester'],
                'batch' => $def['batch'],
            ]);

            foreach ($def['applications'] as $appDef) {
                Application::createWithApplicationNumber(array_merge(
                    [
                        'student_id' => $student->id,
                        'department_id' => $department->id,
                        'category_id' => $categories[$appDef['category']]->id,
                        'semester_at_submission' => $student->current_semester,
                    ],
                    self::applicationAttributes($appDef),
                ));
            }
        }
    }

    /**
     * The realistic demo dataset, shared with `demo:refresh` so a fresh
     * seed and an in-place refresh always produce the same kind of data.
     *
     * @return array<int, array{name: string, program_code: string, batch: string, sequence: int, current_semester: int, applications: array<int, array<string, string>>}>
     */
    public static function demoStudents(): array
    {
        return [
            [
                'name' => 'Hamza Ali',
                'program_code' => 'BSSE',
                'batch' => 'F22',
                'sequence' => 101,
                'current_semester' => 7,
                'applications' => [
                    [
                        'category' => 'Fee Issue',
                        'status' => 'resolved',
                        'priority' => 'high',
                        'subject' => 'Fee challan not updated after payment',
                        'body' => 'I paid my semester fee challan through Bank Al Habib on 2 October but the portal still shows it as unpaid. Please update my fee status so I am not marked as a defaulter.',
                        'resolution_note' => 'Payment verified with the bank statement and the portal has been updated. The fee status now shows as paid.',
                    ],
                    [
                        'category' => 'Attendance Issue',
                        'status' => 'resolved',
                        'priority' => 'normal',
                        'subject' => 'Marked absent in OOP lecture while present',
                        'body' => 'I was present in the Object Oriented Programming lecture on 6 October but the attendance sheet shows me as absent. Several classmates can confirm I was there.',
                        'resolution_note' => 'Checked with the course instructor and the attendance record has been corrected.',
                    ],
                    [
                        'category' => 'LMS/Portal Issue',
                        'status' => 'rejected',
                        'priority' => 'normal',
                        'subject' => 'Cannot access recorded lecture videos on LMS',
                        'body' => 'The recorded lectures for Database Systems are not loading on the LMS portal. I get a blank page every time I try to open them.',
                        'rejection_reason' => 'This is a known, temporary LMS outage already reported to the vendor. No department-level action is needed; please retry after tonight\'s maintenance window ends.',
                    ],
                ],
            ],
            [
                'name' => 'Abdullah Khan',
                'program_code' => 'BSSE',
                'batch' => 'F23',
                'sequence' => 102,
                'current_semester' => 5,
                'applications' => [
                    [
                        'category' => 'Examination Issue',
                        'status' => 'under_review',
                        'priority' => 'high',
                        'subject' => 'Exam result not updated on ERP for Calculus',
                        'body' => 'My result for the Calculus-II midterm exam has not appeared on the ERP even though the exam was conducted three weeks ago. My classmates have already received their marks.',
                    ],
                    [
                        'category' => 'Academic Issue',
                        'status' => 'closed',
                        'priority' => 'urgent',
                        'subject' => 'Course registration for FYP-II not allowed',
                        'body' => 'The ERP is not letting me register for FYP-II this semester even though I have cleared all prerequisite courses. I need this resolved before the registration deadline closes.',
                    ],
                    [
                        'category' => 'Other',
                        'status' => 'under_review',
                        'priority' => 'urgent',
                        'subject' => 'Request to change elective course before deadline',
                        'body' => 'I would like to switch my elective from Artificial Intelligence to Information Security before the add/drop deadline, as the AI section clashes with another core course.',
                    ],
                ],
            ],
            [
                'name' => 'Ali Hassan',
                'program_code' => 'BSSE',
                'batch' => 'S23',
                'sequence' => 103,
                'current_semester' => 5,
                'applications' => [
                    [
                        'category' => 'Result/Grade Issue',
                        'status' => 'resolved',
                        'priority' => 'urgent',
                        'subject' => 'Grade dispute for Software Engineering project',
                        'body' => 'I believe my project grade for Software Engineering was entered incorrectly. My group was awarded a B- but the rubric score sheet given to us shows an A grade.',
                        'resolution_note' => 'Reviewed the rubric with the course instructor and confirmed a data-entry error. The grade has been corrected to A on the ERP.',
                    ],
                    [
                        'category' => 'LMS/Portal Issue',
                        'status' => 'submitted',
                        'priority' => 'high',
                        'subject' => 'Unable to submit assignment due to LMS error',
                        'body' => 'The LMS shows a server error whenever I try to upload my Data Structures assignment. The deadline is tomorrow and I am worried about a late submission penalty.',
                    ],
                ],
            ],
            [
                'name' => 'Sarfraz Ahmed',
                'program_code' => 'BSDS',
                'batch' => 'F22',
                'sequence' => 105,
                'current_semester' => 7,
                'applications' => [
                    [
                        'category' => 'Other',
                        'status' => 'submitted',
                        'priority' => 'high',
                        'subject' => 'Need duplicate student ID card',
                        'body' => 'I lost my student ID card last week and need a duplicate issued as soon as possible, since I need it to enter the examination hall next week.',
                    ],
                    [
                        'category' => 'Fee Issue',
                        'status' => 'submitted',
                        'priority' => 'high',
                        'subject' => 'Scholarship discount not reflected in fee challan',
                        'body' => 'My 50% merit scholarship discount has not been applied to this semester\'s fee challan. The full amount is showing as due, which I cannot pay by the deadline.',
                    ],
                ],
            ],
            [
                'name' => 'Mustafa Raza',
                'program_code' => 'BSDS',
                'batch' => 'F23',
                'sequence' => 106,
                'current_semester' => 5,
                'applications' => [
                    [
                        'category' => 'Faculty/Teacher Related',
                        'status' => 'closed',
                        'priority' => 'urgent',
                        'subject' => 'Request to review conduct of a lab instructor',
                        'body' => 'Several students in my section have raised concerns about the lab instructor\'s conduct during the Database Systems lab. I would like to formally request a review.',
                    ],
                    [
                        'category' => 'General Query',
                        'status' => 'resolved',
                        'priority' => 'urgent',
                        'subject' => 'Query about eligibility for final year project extension',
                        'body' => 'I would like to know whether an extension is possible for the FYP-II submission given that one of my group members withdrew from the university mid-semester.',
                        'resolution_note' => 'Confirmed with the FYP coordinator that a two-week extension has been approved for your group due to the member withdrawal.',
                    ],
                    [
                        'category' => 'Academic Issue',
                        'status' => 'submitted',
                        'priority' => 'urgent',
                        'subject' => 'Clash between core course and compulsory lab timing',
                        'body' => 'My timetable has the Operating Systems lecture and the Computer Networks lab scheduled at the same time this semester, making it impossible to attend both.',
                    ],
                ],
            ],
            [
                'name' => 'Laiba Noor',
                'program_code' => 'BSDS',
                'batch' => 'S24',
                'sequence' => 107,
                'current_semester' => 2,
                'applications' => [
                    [
                        'category' => 'Other',
                        'status' => 'resolved',
                        'priority' => 'normal',
                        'subject' => 'Request for official transcript for internship application',
                        'body' => 'I need an official transcript issued urgently as I have to submit it along with my internship application by the end of this week.',
                        'resolution_note' => 'The transcript has been processed and is ready for collection from the Examination Department.',
                    ],
                ],
            ],
            [
                'name' => 'Ayesha Fatima',
                'program_code' => 'BSAI',
                'batch' => 'F23',
                'sequence' => 108,
                'current_semester' => 5,
                'applications' => [
                    [
                        'category' => 'Academic Issue',
                        'status' => 'closed',
                        'priority' => 'normal',
                        'subject' => 'Incorrect semester shown on student profile',
                        'body' => 'My student profile on the portal shows me in the 5th semester, but I am actually enrolled in the 6th semester this term.',
                    ],
                    [
                        'category' => 'Timetable Issue',
                        'status' => 'closed',
                        'priority' => 'normal',
                        'subject' => 'Overlapping timetable for two compulsory courses',
                        'body' => 'Software Quality Engineering and Human Computer Interaction have been scheduled in overlapping slots this semester, and both are compulsory for my batch.',
                    ],
                    [
                        'category' => 'Academic Issue',
                        'status' => 'in_progress',
                        'priority' => 'high',
                        'subject' => 'Request to join a different section for Software Design',
                        'body' => 'I would like to move from Section A to Section B for Software Design, as my current section\'s timing clashes with my part-time job.',
                    ],
                ],
            ],
            [
                'name' => 'Fatima Zahra',
                'program_code' => 'BSAI',
                'batch' => 'F24',
                'sequence' => 109,
                'current_semester' => 3,
                'applications' => [
                    [
                        'category' => 'General Query',
                        'status' => 'resolved',
                        'priority' => 'normal',
                        'subject' => 'Query about re-take policy for a failed course',
                        'body' => 'I failed Discrete Mathematics last semester and would like to know the process and deadline for registering it as a re-take course.',
                        'resolution_note' => 'Re-take registration opens next week; please visit the Academics Office with your transcript to complete the process.',
                    ],
                ],
            ],
            [
                'name' => 'Usman Tariq',
                'program_code' => 'BSAI',
                'batch' => 'S23',
                'sequence' => 110,
                'current_semester' => 5,
                'applications' => [
                    [
                        'category' => 'Timetable Issue',
                        'status' => 'resolved',
                        'priority' => 'normal',
                        'subject' => 'Clash between elective and compulsory lab session',
                        'body' => 'My elective course Cloud Computing has been scheduled at the same time as the compulsory Software Engineering lab, and I cannot attend both.',
                        'resolution_note' => 'The Cloud Computing elective section has been moved to a new time slot. Please check the updated timetable on the portal.',
                    ],
                ],
            ],
            [
                'name' => 'Hira Shahid',
                'program_code' => 'BSSE',
                'batch' => 'F24',
                'sequence' => 104,
                'current_semester' => 3,
                'applications' => [
                    [
                        'category' => 'Academic Issue',
                        'status' => 'closed',
                        'priority' => 'high',
                        'subject' => 'Incomplete grade shown for a completed course',
                        'body' => 'My transcript shows an incomplete (I) grade for Technical and Business Writing, even though I completed and submitted all required coursework.',
                    ],
                    [
                        'category' => 'Timetable Issue',
                        'status' => 'under_review',
                        'priority' => 'normal',
                        'subject' => 'Request to shift lab session to a different day',
                        'body' => 'I have a medical appointment every Wednesday afternoon that clashes with my Software Engineering lab. Could the lab be moved to another day for my section?',
                    ],
                    [
                        'category' => 'Registration Issue',
                        'status' => 'under_review',
                        'priority' => 'urgent',
                        'subject' => 'Unable to register for a prerequisite course',
                        'body' => 'The ERP is blocking my registration for Database Systems, saying I have not completed the prerequisite, even though I passed it last semester.',
                    ],
                ],
            ],
        ];
    }

    /**
     * @param  array{program_code: string, batch: string, sequence: int}  $def
     */
    public static function registrationNo(array $def): string
    {
        return sprintf('SU92-%sM-%s-%03d', $def['program_code'], $def['batch'], $def['sequence']);
    }

    /**
     * Maps a demo application definition to Application attributes, adding
     * resolved_at/closed_at to match whichever status was given.
     *
     * @param  array<string, string>  $appDef
     * @return array<string, mixed>
     */
    public static function applicationAttributes(array $appDef): array
    {
        $status = ApplicationStatus::from($appDef['status']);

        return [
            'subject' => $appDef['subject'],
            'body' => $appDef['body'],
            'priority' => $appDef['priority'],
            'status' => $status,
            'resolution_note' => $appDef['resolution_note'] ?? null,
            'rejection_reason' => $appDef['rejection_reason'] ?? null,
            'resolved_at' => $status === ApplicationStatus::Resolved ? now() : null,
            'closed_at' => $status === ApplicationStatus::Closed ? now() : null,
        ];
    }
}
