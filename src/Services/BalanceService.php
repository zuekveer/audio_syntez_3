<?php

declare(strict_types=1);

namespace App\Services;

use App\Entity\Users;
use App\Entity\Wallet;
use App\Exception\AccessDeniedException;
use App\Exception\NotEnoughMoneyException;
use App\Exception\NotFoundException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class BalanceService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly Security $security,
    ) {
    }

    /**
     * @throws NotFoundException
     */
    public function getBalance(): Wallet
    {
        $wallet = $this->entityManager->getRepository(Wallet::class)->findOneBy(['user' => $this->security->getUser()]); // was del ->getUserIdentifier()
        if (null === $wallet) {
            throw new NotFoundException();
        }

        return $wallet;
    }

    /**
     * @throws NotFoundException
     * @throws AccessDeniedException
     * @throws \Exception
     */
    public function changeBalance(int $count): Wallet
    {
        $wallet = $this->entityManager->getRepository(Wallet::class)->findOneBy(['user' => $this->security->getUser()]);
        if (null === $wallet) {
            throw new NotFoundException();
        }

        $balance = $wallet->getBalance();
        if ($this->security->isGranted(Users::ROLE_CUSTOMER) && ($count > 0)) {
            throw new AccessDeniedException();
        }
        $balance = $balance + $count;
        if ($balance < 0) {
            throw new NotEnoughMoneyException();
        }

        $wallet->setBalance($balance);

        $this->entityManager->persist($wallet);
        $this->entityManager->flush();

        return $wallet;
    }
}
