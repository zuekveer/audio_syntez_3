<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Services\MailService;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class MailServiceTest extends TestCase
{
    private MailService $mailService;
    private MockObject $mockObject;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->mockObject = $this->createMock(MailerInterface::class);
        $this->mailService = new MailService($this->mockObject, 'sender@example.com');
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testSendCode(): void
    {
        $recipient = 'recipient@example.com';
        $code = 123456;
        $subject = 'Verification Code';

        $expectedEmail = (new Email())
            ->from('sender@example.com')
            ->to($recipient)
            ->subject($subject)
            ->text($subject.$code);

        $this->mockObject->expects($this->once())
            ->method('send')
            ->with($this->equalTo($expectedEmail));

        $this->mailService->sendCode($recipient, $code, $subject);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testSendPasswordResetConfirmation(): void
    {
        $recipient = 'recipient@example.com';
        $subject = 'Password Reset';

        $expectedEmail = (new Email())
            ->from('sender@example.com')
            ->to($recipient)
            ->subject($subject)
            ->text('Your new password was set');

        $this->mockObject->expects($this->once())
            ->method('send')
            ->with($this->equalTo($expectedEmail));

        $this->mailService->sendPasswordResetConfirmation($recipient);
    }
}
