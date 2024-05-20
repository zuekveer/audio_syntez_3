<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Entity\Users;
use App\Entity\Wallet;
use App\Exception\AccessDeniedException;
use App\Exception\NotEnoughMoneyException;
use App\Exception\NotFoundException;
use App\Services\BalanceService;
use App\Tests\Provider\Provider;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;

final class BalanceServiceTest extends TestCase
{
    private MockObject $mockSecurity;
    private MockObject $mockEntityManager;
    private MockObject $mockUserRepository;
    private BalanceService $balanceService;
    private Users $mockUser;
    private Wallet $mockWallet;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->mockSecurity = $this->createMock(Security::class);
        $this->mockEntityManager = $this->createMock(EntityManagerInterface::class);
        $this->mockUserRepository = $this->createMock(EntityRepository::class);
        $this->mockWallet = $this->createMock(Wallet::class);
        $this->mockUser = $this->createMock(Users::class);
        $this->balanceService = new BalanceService($this->mockEntityManager, $this->mockSecurity);
    }

    /**
     * @throws Exception
     * @throws NotFoundException
     */
    #[DataProviderExternal(Provider::class, 'getBalanceProvider')]
    public function testGetBalance(?Wallet $wallet, ?string $userNotFoundException): void
    {
        $this->mockSecurity
            ->method('getUser')
            ->willReturn($this->mockUser);

        $this->mockEntityManager
            ->method('getRepository')
            ->willReturn($this->mockUserRepository);

        $this->mockUserRepository
            ->method('findOneBy')
            ->willReturn($this->mockWallet);

        if (null !== $userNotFoundException) {
            $this->assertEquals(NotFoundException::class, $userNotFoundException);
            $this->assertEquals(null, $wallet);
        } else {
            $this->assertEquals(null, $userNotFoundException);
            $this->assertEquals(new Wallet(), $wallet);
        }
        $this->balanceService->getBalance();
    }

    /**
     * @throws Exception
     */
    #[DataProviderExternal(Provider::class, 'changeBalanceProvider')]
    public function testChangeBalance(
        ?Wallet $wallet,
        ?string $expectedNotFoundException,
        ?string $expectedAccessDeniedException,
        ?string $expectedNotEnoughMoneyException,
        ?bool $role,
        int $changeBalanceStart,
        int $actNumber,
        ?int $finalBalance,
    ): void {
        $this->mockSecurity
            ->method('getUser')
            ->willReturn($this->mockUser);

        $this->mockEntityManager
            ->method('getRepository')
            ->willReturn($this->mockUserRepository);

        $this->mockUserRepository
            ->method('findOneBy')
            ->with(['user' => $this->mockUser])
            ->willReturn($this->mockWallet);

        if (null !== $expectedNotFoundException) {
            $this->assertEquals(NotFoundException::class, $expectedNotFoundException);
        }

        if (null !== $expectedAccessDeniedException) {
            $this->assertEquals(AccessDeniedException::class, $expectedAccessDeniedException);
        }

        if (null !== $expectedNotEnoughMoneyException) {
            $this->assertEquals(NotEnoughMoneyException::class, $expectedNotEnoughMoneyException);
        }

        if (null !== $role) {
            $this->mockSecurity
                ->method('isGranted')
                ->with($this->mockUser::ROLE_CUSTOMER)
                ->willReturn($role);
        }

        $result = $changeBalanceStart + $actNumber;
        $this->assertEquals($finalBalance, $result);
    }
}
