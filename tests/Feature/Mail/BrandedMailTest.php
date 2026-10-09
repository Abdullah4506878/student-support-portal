<?php

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;

test('the verification email carries the no-reply branding', function () {
    $user = User::factory()->create();

    $message = (new VerifyEmail)->toMail($user);

    expect($message->subject)->toBe('[SE Student Support] Verify your email');
    expect($message->replyTo)->toBe([[config('mail.reply_to.address'), config('mail.reply_to.name')]]);
    expect($message->outroLines)->toContain('This is an automated email. Replies to this address are not received. For any query, log in to the portal.');
});

test('the password reset email carries the no-reply branding', function () {
    $user = User::factory()->create();

    $message = (new ResetPassword('test-token'))->toMail($user);

    expect($message->subject)->toBe('[SE Student Support] Reset your password');
    expect($message->replyTo)->toBe([[config('mail.reply_to.address'), config('mail.reply_to.name')]]);
    expect($message->outroLines)->toContain('This is an automated email. Replies to this address are not received. For any query, log in to the portal.');
});
