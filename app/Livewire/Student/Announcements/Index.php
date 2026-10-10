<?php

namespace App\Livewire\Student\Announcements;

use App\Models\Announcement;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Announcements')]
class Index extends Component
{
    use WithPagination;

    public function mount(): void
    {
        $this->authorize('viewAny', Announcement::class);
    }

    /**
     * @return LengthAwarePaginator<int, Announcement>
     */
    #[Computed]
    public function announcements(): LengthAwarePaginator
    {
        return Announcement::query()
            ->where('department_id', Auth::user()->department_id)
            ->visibleToStudents()
            ->orderByRaw('COALESCE(publish_at, created_at) desc')
            ->paginate(10);
    }

    public function render(): View
    {
        return view('livewire.student.announcements.index');
    }
}
