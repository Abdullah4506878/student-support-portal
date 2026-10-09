<?php

namespace App\Livewire\Student\Applications;

use App\Models\Application;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Application Details')]
class Show extends Component
{
    public Application $application;

    public function mount(Application $application): void
    {
        $this->authorize('view', $application);

        $this->application = $application->load([
            'category',
            'attachments',
            'events' => fn ($query) => $query->where('visible_to_student', true)->latest('created_at'),
            'events.user',
        ]);
    }

    public function render(): View
    {
        return view('livewire.student.applications.show');
    }
}
