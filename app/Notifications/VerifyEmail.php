<?php

namespace App\Notifications;

use App\Http\Controllers\Api\V1\User\UserController;
use App\Models\EmailVerification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class VerifyEmail extends Notification implements ShouldQueue
{
    use Queueable;

    private EmailVerification $emailVerification;

    public function __construct(EmailVerification $emailVerification)
    {
        $this->emailVerification = $emailVerification;
    }

    public function via(): array
    {
        return ['mail'];
    }

    public function toMail(): MailMessage
    {
        $uri = URL::action(
            [UserController::class, 'verify'],
            ['verificationToken' => $this->emailVerification->verification_token]
        );

        return (new MailMessage())->action('Confirm Email Address', $uri);
    }
}
