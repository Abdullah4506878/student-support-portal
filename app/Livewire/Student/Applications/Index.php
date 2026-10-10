<?php

namespace App\Livewire\Student\Applications;

use App\Enums\ApplicationStatus;
use App\Models\Application;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('My Applications')]
class Index extends Component
{
    use WithPagination;

    #[Url]
    public string $status = '';

    #[Url]
    public string $search = '';

    public function updated(string $property): void
    {
        if (in_array($property, ['status', 'search'], true)) {
            $this->resetPage();
        }
    }

    public function clearFilters(): void
    {
        $this->reset(['search', 'status']);
        $this->resetPage();
    }

    #[Computed]
    public function hasActiveFilters(): bool
    {
        return $this->search !== '' || $this->status !== '';
    }

    /**
     * @return LengthAwarePaginator<int, Application>
     */
    #[Computed]
    public function applications(): LengthAwarePaginator
    {
        $student = Auth::user()->student;

        return Application::query()
            ->where('student_id', $student->id)
            ->with('category')
            ->when($this->status !== '', fn ($query) => $query->where('status', $this->status))
            ->when($this->search !== '', function ($query) {
                $query->where(function ($query) {
                    $query->where('application_no', 'like', '%'.$this->search.'%')
                        ->orWhere('subject', 'like', '%'.$this->search.'%');
                });
            })
            ->latest()
            ->paginate(10);
    }

    /**
     * @return array<string, string>
     */
    #[Computed]
    public function statuses(): array
    {
        return array_combine(
            array_map(fn (ApplicationStatus $status) => $status->value, ApplicationStatus::cases()),
            array_map(fn (ApplicationStatus $status) => Str::headline($status->value), ApplicationStatus::cases()),
        );
    }

    public function render(): View
    {
        return view('livewire.student.applications.index');
    }
}
