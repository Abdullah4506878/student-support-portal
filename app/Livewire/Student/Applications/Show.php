<?php

namespace App\Livewire\Student\Applications;

use App\Concerns\ApplicationValidationRules;
use App\Enums\ApplicationStatus;
use App\Enums\MessageType;
use App\Models\Application;
use App\Models\ApplicationAttachment;
use App\Models\ApplicationEvent;
use App\Models\ApplicationMessage;
use App\Models\User;
use App\Notifications\StudentRespondedToRequest;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

#[Title('Application Details')]
class Show extends Component
{
    use ApplicationValidationRules, WithFileUploads;

    public const RESPONSE_MAX_LENGTH = 1000;

    public Application $application;

    public string $response_body = '';

    /**
     * @var array<int, TemporaryUploadedFile>
     */
    public array $response_attachments = [];

    public function mount(Application $application): void
    {
        $this->authorize('view', $application);

        $this->application = $application->load(['category', 'attachments']);

        // The student is now looking at this application, so any unread
        // admin-authored messages are no longer "new" to them.
        $this->application->messages()
            ->where('sender_id', '!=', Auth::id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    /**
     * A single chronological feed of this application's status history
     * (only the parts the student is meant to see) and its messages
     * (free-text, requests, responses) — each message appears exactly
     * once here, never duplicated as a separate application_event.
     *
     * @return Collection<int, ApplicationEvent|ApplicationMessage>
     */
    #[Computed]
    public function timeline(): Collection
    {
        $events = $this->application->events()->where('visible_to_student', true)->with('user')->get();
        $messages = $this->application->messages()->with(['sender', 'attachments'])->get();

        return $events->concat($messages)->sortByDesc('created_at')->values();
    }

    #[Computed]
    public function openRequest(): ?ApplicationMessage
    {
        return $this->openRequestQuery()->latest()->first();
    }

    public function responseMaxLength(): int
    {
        return self::RESPONSE_MAX_LENGTH;
    }

    public function removeResponseAttachment(int $index): void
    {
        unset($this->response_attachments[$index]);
        $this->response_attachments = array_values($this->response_attachments);
    }

    public function respond(): void
    {
        $this->authorize('respondToRequest', $this->application);

        $openRequest = $this->openRequestQuery()->latest()->first();
        abort_if($openRequest === null, 403);

        if (trim($this->response_body) === '' && count($this->response_attachments) === 0) {
            $this->addError('response_body', __('Enter a response or attach at least one file.'));

            return;
        }

        $rules = [
            'response_body' => ['nullable', 'string', 'max:'.self::RESPONSE_MAX_LENGTH],
            'response_attachments' => $this->attachmentsRules(),
            'response_attachments.*' => $this->attachmentRules(),
        ];
        $messages = $this->attachmentMessages($this->response_attachments);

        try {
            $validated = $this->validate($rules, $messages);
        } catch (ValidationException $e) {
            foreach ($this->invalidAttachmentIndexes($e->validator->errors()) as $index) {
                unset($this->response_attachments[$index]);
            }
            $this->response_attachments = array_values($this->response_attachments);

            throw $e;
        }

        DB::transaction(function () use ($openRequest, $validated) {
            $response = ApplicationMessage::create([
                'application_id' => $this->application->id,
                'sender_id' => Auth::id(),
                'parent_id' => $openRequest->id,
                'type' => MessageType::StudentResponse,
                'body' => $validated['response_body'] ?? '',
            ]);

            foreach ($this->response_attachments as $file) {
                $path = $file->store('attachments/'.$this->application->id, 'local');

                ApplicationAttachment::create([
                    'application_id' => $this->application->id,
                    'message_id' => $response->id,
                    'uploaded_by' => Auth::id(),
                    'original_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'mime_type' => $file->getMimeType(),
                    'size' => $file->getSize(),
                ]);
            }

            $openRequest->update(['responded_at' => now()]);

            // No separate "status_changed" event here — the response
            // message itself is the one timeline entry for this action,
            // same as how an admin's request doesn't get one either.
            $this->application->update(['status' => ApplicationStatus::UnderReview]);
        });

        $this->response_body = '';
        $this->response_attachments = [];
        unset($this->openRequest, $this->timeline);

        Notification::send(
            User::adminOfficersInDepartment($this->application->department_id),
            new StudentRespondedToRequest($this->application),
        );

        Flux::toast(variant: 'success', text: __('Response sent.'));
    }

    /**
     * @return HasMany<ApplicationMessage, Application>
     */
    private function openRequestQuery(): HasMany
    {
        return $this->application->messages()
            ->whereIn('type', [MessageType::InfoRequest->value, MessageType::DocumentRequest->value])
            ->whereNull('responded_at')
            ->whereNull('cancelled_at');
    }

    public function render(): View
    {
        return view('livewire.student.applications.show');
    }
}
