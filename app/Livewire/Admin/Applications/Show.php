<?php

namespace App\Livewire\Admin\Applications;

use App\Enums\ApplicationEventType;
use App\Enums\ApplicationPriority;
use App\Enums\ApplicationStatus;
use App\Models\Application;
use App\Models\ApplicationEvent;
use App\Models\InternalNote;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Application Details')]
class Show extends Component
{
    /**
     * Status values selectable from the generic "change status" action.
     * Resolve/reject/close are their own dedicated actions because they
     * require extra data (a note or reason) and set their own timestamp.
     */
    private const SELECTABLE_STATUSES = [
        'submitted',
        'under_review',
        'info_required',
        'in_progress',
    ];

    public Application $application;

    public string $priority_input = '';

    public string $status_input = '';

    public string $resolution_note = '';

    public string $rejection_reason = '';

    public string $new_note = '';

    public ?int $editing_note_id = null;

    public string $editing_note_body = '';

    public function mount(Application $application): void
    {
        $this->authorize('view', $application);

        $this->application = $application->load(['category', 'attachments', 'student.user']);
        $this->priority_input = $application->priority->value;
    }

    /**
     * @return Collection<int, ApplicationEvent>
     */
    #[Computed]
    public function timelineEvents(): Collection
    {
        return $this->application->events()->with('user')->latest('created_at')->get();
    }

    /**
     * @return Collection<int, InternalNote>
     */
    #[Computed]
    public function internalNotes(): Collection
    {
        return $this->application->internalNotes()->with('admin')->latest()->get();
    }

    /**
     * @return Collection<int, Application>
     */
    #[Computed]
    public function previousApplications(): Collection
    {
        return Application::query()
            ->where('student_id', $this->application->student_id)
            ->where('id', '!=', $this->application->id)
            ->with('category')
            ->latest()
            ->get();
    }

    #[Computed]
    public function isFinal(): bool
    {
        return in_array($this->application->status, [ApplicationStatus::Closed, ApplicationStatus::Rejected], true);
    }

    public function updatePriority(): void
    {
        $this->authorize('updatePriority', $this->application);
        $this->guardNotFinal();

        $validated = $this->validate([
            'priority_input' => ['required', Rule::in(array_column(ApplicationPriority::cases(), 'value'))],
        ])['priority_input'];

        if ($validated === $this->application->priority->value) {
            return;
        }

        $from = $this->application->priority->value;

        $this->application->update(['priority' => $validated]);

        ApplicationEvent::create([
            'application_id' => $this->application->id,
            'user_id' => Auth::id(),
            'event_type' => ApplicationEventType::PriorityChanged,
            'from_value' => $from,
            'to_value' => $validated,
            'visible_to_student' => false,
        ]);

        unset($this->timelineEvents);

        Flux::toast(variant: 'success', text: __('Priority updated.'));
    }

    public function updateStatus(): void
    {
        $this->authorize('updateStatus', $this->application);
        $this->guardNotFinal();

        $validated = $this->validate([
            'status_input' => ['required', Rule::in(self::SELECTABLE_STATUSES)],
        ])['status_input'];

        if ($validated === $this->application->status->value) {
            return;
        }

        $from = $this->application->status->value;

        $this->application->update(['status' => $validated]);

        ApplicationEvent::create([
            'application_id' => $this->application->id,
            'user_id' => Auth::id(),
            'event_type' => ApplicationEventType::StatusChanged,
            'from_value' => $from,
            'to_value' => $validated,
            'visible_to_student' => true,
        ]);

        unset($this->timelineEvents);

        Flux::toast(variant: 'success', text: __('Status updated.'));
    }

    public function resolve(): void
    {
        $this->authorize('resolve', $this->application);
        $this->guardNotFinal();

        $validated = $this->validate([
            'resolution_note' => ['required', 'string', 'max:1000'],
        ])['resolution_note'];

        $from = $this->application->status->value;

        $this->application->update([
            'status' => ApplicationStatus::Resolved,
            'resolution_note' => $validated,
            'resolved_at' => now(),
        ]);

        ApplicationEvent::create([
            'application_id' => $this->application->id,
            'user_id' => Auth::id(),
            'event_type' => ApplicationEventType::Resolved,
            'from_value' => $from,
            'to_value' => ApplicationStatus::Resolved->value,
            'visible_to_student' => true,
        ]);

        unset($this->timelineEvents);

        Flux::toast(variant: 'success', text: __('Application marked resolved.'));
    }

    public function reject(): void
    {
        $this->authorize('reject', $this->application);
        $this->guardNotFinal();

        $validated = $this->validate([
            'rejection_reason' => ['required', 'string', 'max:1000'],
        ])['rejection_reason'];

        $from = $this->application->status->value;

        $this->application->update([
            'status' => ApplicationStatus::Rejected,
            'rejection_reason' => $validated,
        ]);

        ApplicationEvent::create([
            'application_id' => $this->application->id,
            'user_id' => Auth::id(),
            'event_type' => ApplicationEventType::Rejected,
            'from_value' => $from,
            'to_value' => ApplicationStatus::Rejected->value,
            'visible_to_student' => true,
        ]);

        unset($this->timelineEvents);

        Flux::toast(variant: 'success', text: __('Application rejected.'));
    }

    public function close(): void
    {
        $this->authorize('close', $this->application);
        $this->guardNotFinal();

        $from = $this->application->status->value;

        $this->application->update([
            'status' => ApplicationStatus::Closed,
            'closed_at' => now(),
        ]);

        ApplicationEvent::create([
            'application_id' => $this->application->id,
            'user_id' => Auth::id(),
            'event_type' => ApplicationEventType::Closed,
            'from_value' => $from,
            'to_value' => ApplicationStatus::Closed->value,
            'visible_to_student' => true,
        ]);

        unset($this->timelineEvents);

        Flux::toast(variant: 'success', text: __('Application closed.'));
    }

    public function addNote(): void
    {
        $this->authorize('create', [InternalNote::class, $this->application]);

        $validated = $this->validate([
            'new_note' => ['required', 'string', 'max:2000'],
        ])['new_note'];

        InternalNote::create([
            'application_id' => $this->application->id,
            'admin_id' => Auth::id(),
            'body' => $validated,
        ]);

        $this->new_note = '';
        unset($this->internalNotes);

        Flux::toast(variant: 'success', text: __('Note added.'));
    }

    public function startEditingNote(int $noteId): void
    {
        $note = InternalNote::findOrFail($noteId);
        $this->authorize('update', $note);

        $this->editing_note_id = $noteId;
        $this->editing_note_body = $note->body;
    }

    public function cancelEditingNote(): void
    {
        $this->editing_note_id = null;
        $this->editing_note_body = '';
    }

    public function saveNote(): void
    {
        $note = InternalNote::findOrFail($this->editing_note_id);
        $this->authorize('update', $note);

        $validated = $this->validate([
            'editing_note_body' => ['required', 'string', 'max:2000'],
        ])['editing_note_body'];

        $note->update(['body' => $validated]);

        $this->editing_note_id = null;
        $this->editing_note_body = '';
        unset($this->internalNotes);

        Flux::toast(variant: 'success', text: __('Note updated.'));
    }

    private function guardNotFinal(): void
    {
        abort_if($this->isFinal(), 403);
    }

    public function render(): View
    {
        return view('livewire.admin.applications.show');
    }
}
