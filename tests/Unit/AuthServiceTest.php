<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Entity\Users;
use App\Exception\AccessDeniedException;
use App\Exception\DuplicateException;
use App\Exception\NotFoundException;
use App\Services\AuthService;
use App\Services\MailService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AuthServiceTest extends TestCase
{
    private MockObject $mockEntityManager;
    private MockObject $mockUserRepository;
    private AuthService $authService;
    private Users $user;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->mockEntityManager = $this->createMock(EntityManagerInterface::class);
        $this->mockUserRepository = $this->createMock(EntityRepository::class);
        $mockMailerInterface = $this->createMock(MailService::class);
        $this->user = (new Users())->setToken(012345);
        $mockPasswordHasher = $this->createMock(UserPasswordHasherInterface::class);

        $this->mockEntityManager
            ->method('getRepository')
            ->with(Users::class)
            ->willReturn($this->mockUserRepository);

        $this->authService = new AuthService(
            $this->mockEntityManager,
            $mockPasswordHasher,
            $mockMailerInterface,
        );
    }

    /**
     * @throws DuplicateException
     * @throws TransportExceptionInterface
     */
    public function testRegisterSuccess(): void
    {
        $this->mockUserRepository
            ->method('findOneBy')
            ->willReturn(null);

        $this->mockEntityManager
            ->expects($this->once())
            ->method('flush');

        $this->authService->registration('test@test.test', 'test', 'test', 'test');
    }

    /**
     * @throws DuplicateException
     * @throws TransportExceptionInterface
     */
    public function testRegisterDuplicateException(): void
    {
        $this->mockUserRepository
            ->method('findOneBy')
            ->willReturn($this->user);

        self::expectException(DuplicateException::class);
        $this->authService->registration('test@test.test', 'test', 'test', 'test');
    }

    /**
     * @throws NotFoundException
     * @throws TransportExceptionInterface
     */
    public function testSendResetCodeSuccess(): void
    {
        $this->mockUserRepository
            ->method('findOneBy')
            ->willReturn($this->user);

        $this->mockUserRepository
            ->expects(self::once())
            ->method('findOneBy')
            ->willReturn($this->user);

        $this->mockEntityManager
            ->expects(self::once())
            ->method('flush');

        $this->authService->resetPassword('test@test.test');
    }

    /**
     * @throws NotFoundException
     * @throws TransportExceptionInterface
     */
    public function testSendResetCodeUserNotFoundException(): void
    {
        $this->mockUserRepository
            ->method('findOneBy')
            ->willReturn(null);

        self::expectException(NotFoundException::class);
        $this->authService->resetPassword('test@test.test');
    }

    /**
     * @throws NotFoundException
     * @throws AccessDeniedException
     * @throws \Exception
     * @throws TransportExceptionInterface
     */
    public function testResetPasswordSuccess(): void
    {
        $this->mockUserRepository
            ->method('findOneBy')
            ->willReturn($this->user);

        $this->mockUserRepository
            ->expects(self::once())
            ->method('findOneBy')
            ->willReturn($this->user);

        $this->mockEntityManager
            ->expects(self::once())
            ->method('flush');

        $this->authService->reset('test@test.test', (int) $this->user->getToken(), 'test');
    }

    /**
     * @throws AccessDeniedException
     * @throws NotFoundException
     * @throws TransportExceptionInterface
     */
    public function testResetPasswordUserNotFoundException(): void
    {
        $this->mockUserRepository
            ->method('findOneBy')
            ->willReturn(null);

        self::expectException(NotFoundException::class);
        $this->authService->reset('test@test.test', 012345, 'test');
    }

    /**
     * @throws AccessDeniedException
     * @throws NotFoundException
     * @throws TransportExceptionInterface
     */
    public function testResetPasswordAccessDeniedException(): void
    {
        $this->mockUserRepository
            ->method('findOneBy')
            ->willReturn($this->user);

        self::expectException(AccessDeniedException::class);
        $this->authService->reset('test@test.test', 543210, 'test');
    }

    /**
     * @throws NotFoundException
     * @throws AccessDeniedException
     */
    public function testVerifyCodeSuccess(): void
    {
        $this->mockUserRepository
            ->expects(self::once())
            ->method('findOneBy')
            ->willReturn($this->user);

        $this->mockEntityManager
            ->expects(self::once())
            ->method('flush');

        $this->authService->verifyCode('test@test.test', (int) $this->user->getToken());
    }

    /**
     * @throws AccessDeniedException
     * @throws NotFoundException
     */
    public function testVerifyCodeyUserNotFoundException(): void
    {
        $this->mockUserRepository
            ->method('findOneBy')
            ->willReturn(null);

        self::expectException(NotFoundException::class);
        $this->authService->verifyCode('test@test.test', 012345);
    }
}
