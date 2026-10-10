<?php

namespace App\Livewire\Student\Applications;

use App\Concerns\ApplicationValidationRules;
use App\Enums\ApplicationEventType;
use App\Models\Application;
use App\Models\ApplicationAttachment;
use App\Models\ApplicationCategory;
use App\Models\ApplicationEvent;
use App\Models\User;
use App\Notifications\ApplicationSubmitted;
use App\Notifications\NewApplicationSubmitted;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

#[Title('New Application')]
class Create extends Component
{
    use ApplicationValidationRules, WithFileUploads;

    public string $category_id = '';

    public string $subject = '';

    public string $body = '';

    /**
     * @var array<int, TemporaryUploadedFile>
     */
    public array $attachments = [];

    public bool $submitting = false;

    public function mount(): void
    {
        $this->authorize('create', Application::class);
    }

    /**
     * @return Collection<int, ApplicationCategory>
     */
    #[Computed]
    public function categories(): Collection
    {
        return ApplicationCategory::query()
            ->where('department_id', Auth::user()->department_id)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();
    }

    #[Computed]
    public function subjectMax(): int
    {
        return $this->subjectMaxLength();
    }

    #[Computed]
    public function bodyMax(): int
    {
        return $this->bodyMaxLength();
    }

    public function removeAttachment(int $index): void
    {
        unset($this->attachments[$index]);
        $this->attachments = array_values($this->attachments);
    }

    public function submit(): void
    {
        $this->authorize('create', Application::class);

        if ($this->submitting) {
            return;
        }

        $this->submitting = true;

        $student = Auth::user()->student;

        $rules = [
            'category_id' => $this->categoryIdRules(Auth::user()->department_id),
            'subject' => $this->subjectRules(),
            'body' => $this->bodyRules(),
            'attachments' => $this->attachmentsRules(),
            'attachments.*' => $this->attachmentRules(),
        ];

        $messages = array_merge($this->applicationMessages(), $this->attachmentMessages($this->attachments));

        try {
            $validated = $this->validate($rules, $messages);
        } catch (\Throwable $e) {
            $this->submitting = false;

            if ($e instanceof ValidationException) {
                foreach ($this->invalidAttachmentIndexes($e->validator->errors()) as $index) {
                    unset($this->attachments[$index]);
                }

                $this->attachments = array_values($this->attachments);
            }

            throw $e;
        }

        $application = DB::transaction(function () use ($validated, $student) {
            $application = Application::createWithApplicationNumber([
                'student_id' => $student->id,
                'department_id' => Auth::user()->department_id,
                'category_id' => $validated['category_id'],
                'subject' => $validated['subject'],
                'body' => $validated['body'],
                'semester_at_submission' => $student->current_semester,
            ]);

            foreach ($this->attachments as $file) {
                $path = $file->store('attachments/'.$application->id, 'local');

                ApplicationAttachment::create([
                    'application_id' => $application->id,
                    'uploaded_by' => Auth::id(),
                    'original_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'mime_type' => $file->getMimeType(),
                    'size' => $file->getSize(),
                ]);
            }

            ApplicationEvent::create([
                'application_id' => $application->id,
                'user_id' => Auth::id(),
                'event_type' => ApplicationEventType::Submitted,
                'visible_to_student' => true,
            ]);

            return $application;
        });

        Auth::user()->notify(new ApplicationSubmitted($application));
        Notification::send(User::adminOfficersInDepartment($application->department_id), new NewApplicationSubmitted($application));

        Flux::toast(variant: 'success', text: __('Application submitted.'));

        $this->redirect(route('student.applications.show', $application), navigate: true);
    }

    public function render(): View
    {
        return view('livewire.student.applications.create');
    }
}
