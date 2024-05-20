<?php

declare(strict_types=1);

namespace App\Services;

use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class MailService
{
    public const SUBJECT_VERIFICATION = 'Verification Code';
    public const SUBJECT_RESET = 'Code to reset password';

    public function __construct(
        private readonly MailerInterface $mailer,
        private readonly string $emailSender)
    {
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function sendCode(string $recipient, int $code, string $subject): void
    {
        $message = (new Email())
            ->from($this->emailSender)
            ->to($recipient)
            ->subject($subject)
            ->text($subject.$code);

        $this->mailer->send($message);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function sendPasswordResetConfirmation(string $recipient): void
    {
        $message = (new Email())
            ->from($this->emailSender)
            ->to($recipient)
            ->subject('Password Reset')
            ->text('Your new password was set');

        $this->mailer->send($message);
    }
}
