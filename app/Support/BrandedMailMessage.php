<?php

namespace App\Support;

use Illuminate\Notifications\Messages\MailMessage;

/**
 * Applies the department's no-reply branding to an outgoing notification
 * mail message: a subject prefix, a Reply-To address, and a closing note
 * that replies to the email are not received.
 */
class BrandedMailMessage
{
    public static function finalize(MailMessage $message, string $subject): MailMessage
    {
        return $message
            ->subject(config('mail.subject_prefix').' '.$subject)
            ->replyTo(config('mail.reply_to.address'), config('mail.reply_to.name'))
            ->line(__('This is an automated email. Replies to this address are not received. For any query, log in to the portal.'));
    }
}
