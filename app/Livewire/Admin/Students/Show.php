<?php

namespace App\Livewire\Admin\Students;

use App\Enums\UserStatus;
use App\Models\Application;
use App\Models\Student;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Student Profile')]
class Show extends Component
{
    public Student $student;

    public string $name = '';

    public function mount(Student $student): void
    {
        $this->authorize('view', $student);

        $this->student = $student->load('user');
        $this->name = $student->user->name;
    }

    /**
     * @return Collection<int, Application>
     */
    #[Computed]
    public function applications(): Collection
    {
        return Application::query()
            ->where('student_id', $this->student->id)
            ->with('category')
            ->latest()
            ->get();
    }

    #[Computed]
    public function isSuspended(): bool
    {
        return $this->student->user->status === UserStatus::Suspended;
    }

    public function updateName(): void
    {
        $this->authorize('updateName', $this->student);

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
        ])['name'];

        $this->student->user->update(['name' => $validated]);

        Flux::toast(variant: 'success', text: __('Name updated.'));
    }

    public function suspend(): void
    {
        $this->authorize('suspend', $this->student);

        $this->student->user->update(['status' => UserStatus::Suspended]);

        Flux::toast(variant: 'success', text: __('Student suspended.'));
    }

    public function reactivate(): void
    {
        $this->authorize('reactivate', $this->student);

        $this->student->user->update(['status' => UserStatus::Active]);

        Flux::toast(variant: 'success', text: __('Student reactivated.'));
    }

    public function render(): View
    {
        return view('livewire.admin.students.show');
    }
}
