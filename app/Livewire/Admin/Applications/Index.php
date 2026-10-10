<?php

namespace App\Livewire\Admin\Applications;

use App\Enums\ApplicationPriority;
use App\Enums\ApplicationStatus;
use App\Models\Application;
use App\Models\ApplicationCategory;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Applications')]
class Index extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $status = '';

    #[Url]
    public string $priority = '';

    #[Url]
    public string $category_id = '';

    #[Url]
    public string $semester = '';

    #[Url]
    public string $date_from = '';

    #[Url]
    public string $date_to = '';

    #[Url]
    public string $sort = 'created_at';

    #[Url]
    public string $direction = 'desc';

    public function mount(): void
    {
        $this->authorize('viewAny', Application::class);
    }

    public function updated(string $property): void
    {
        if (in_array($property, ['search', 'status', 'priority', 'category_id', 'semester', 'date_from', 'date_to'], true)) {
            $this->resetPage();
        }
    }

    public function sortBy(string $column): void
    {
        if ($this->sort === $column) {
            $this->direction = $this->direction === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sort = $column;
            $this->direction = 'asc';
        }
    }

    public function clearFilters(): void
    {
        $this->reset(['search', 'status', 'priority', 'category_id', 'semester', 'date_from', 'date_to']);
        $this->resetPage();
    }

    #[Computed]
    public function hasActiveFilters(): bool
    {
        return $this->search !== ''
            || $this->status !== ''
            || $this->priority !== ''
            || $this->category_id !== ''
            || $this->semester !== ''
            || $this->date_from !== ''
            || $this->date_to !== '';
    }

    /**
     * @return LengthAwarePaginator<int, Application>
     */
    #[Computed]
    public function applications(): LengthAwarePaginator
    {
        $query = Application::query()
            ->with(['category', 'student.user'])
            ->when($this->search !== '', function (Builder $query) {
                $query->where(function (Builder $query) {
                    $query->where('application_no', 'like', '%'.$this->search.'%')
                        ->orWhereHas('student.user', fn (Builder $q) => $q->where('name', 'like', '%'.$this->search.'%'))
                        ->orWhereHas('student', fn (Builder $q) => $q->where('registration_no', 'like', '%'.$this->search.'%'));
                });
            })
            ->when($this->status !== '', fn (Builder $query) => $query->where('status', $this->status))
            ->when($this->priority !== '', fn (Builder $query) => $query->where('priority', $this->priority))
            ->when($this->category_id !== '', fn (Builder $query) => $query->where('category_id', $this->category_id))
            ->when($this->semester !== '', fn (Builder $query) => $query->where('semester_at_submission', $this->semester))
            ->when($this->date_from !== '', fn (Builder $query) => $query->whereDate('created_at', '>=', $this->date_from))
            ->when($this->date_to !== '', fn (Builder $query) => $query->whereDate('created_at', '<=', $this->date_to));

        if ($this->sort === 'priority') {
            $query->orderByPrioritySeverity($this->direction);
        } else {
            $query->orderBy('created_at', $this->direction === 'asc' ? 'asc' : 'desc');
        }

        return $query->paginate(15);
    }

    /**
     * @return Collection<int, ApplicationCategory>
     */
    #[Computed]
    public function categories(): Collection
    {
        return ApplicationCategory::query()->orderBy('sort_order')->get();
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

    /**
     * @return array<string, string>
     */
    #[Computed]
    public function priorities(): array
    {
        return array_combine(
            array_map(fn (ApplicationPriority $priority) => $priority->value, ApplicationPriority::cases()),
            array_map(fn (ApplicationPriority $priority) => Str::headline($priority->value), ApplicationPriority::cases()),
        );
    }

    public function render(): View
    {
        return view('livewire.admin.applications.index');
    }
}
