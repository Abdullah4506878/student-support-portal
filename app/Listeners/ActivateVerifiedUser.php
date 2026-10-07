<?php

namespace App\Listeners;

use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Auth\Events\Verified;

class ActivateVerifiedUser
{
    public function handle(Verified $event): void
    {
        /** @var User $user */
        $user = $event->user;

        if ($user->status === UserStatus::PendingVerification) {
            $user->update(['status' => UserStatus::Active]);
        }
    }
}
