<?php

namespace App\Livewire\Student;

use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('My Profile')]
class Profile extends Component
{
    public int $current_semester;

    public function mount(): void
    {
        $this->current_semester = Auth::user()->student->current_semester;
    }

    public function updateSemester(): void
    {
        $validated = $this->validate([
            'current_semester' => ['required', 'integer', 'between:1,8'],
        ]);

        Auth::user()->student->update($validated);

        Flux::toast(variant: 'success', text: __('Semester updated.'));
    }
}
