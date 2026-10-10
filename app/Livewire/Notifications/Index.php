<?php

namespace App\Livewire\Notifications;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\View;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Notifications')]
class Index extends Component
{
    use WithPagination;

    /**
     * @return LengthAwarePaginator<int, DatabaseNotification>
     */
    #[Computed]
    public function notifications(): LengthAwarePaginator
    {
        return Auth::user()->notifications()->latest()->paginate(15);
    }

    #[Computed]
    public function unreadCount(): int
    {
        return Auth::user()->unreadNotifications()->count();
    }

    /**
     * Marks the notification read and returns where it points to, so the
     * trigger can navigate there — scoped to the current user's own
     * notifications, so no one can touch another user's by guessing an id.
     */
    public function open(string $notificationId): void
    {
        $notification = Auth::user()->notifications()->findOrFail($notificationId);
        $notification->markAsRead();

        $this->dispatch('notifications-updated');
        $this->redirect($notification->data['url'], navigate: true);
    }

    public function markAsRead(string $notificationId): void
    {
        Auth::user()->notifications()->findOrFail($notificationId)->markAsRead();

        unset($this->notifications, $this->unreadCount);
        $this->dispatch('notifications-updated');
    }

    public function markAllAsRead(): void
    {
        Auth::user()->unreadNotifications->markAsRead();

        unset($this->notifications, $this->unreadCount);
        $this->dispatch('notifications-updated');
    }

    public function render(): View
    {
        return view('livewire.notifications.index');
    }
}
