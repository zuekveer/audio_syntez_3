<?php

declare(strict_types=1);

namespace App\Services;

use App\Entity\Users;
use App\Entity\Wallet;
use App\Exception\AccessDeniedException;
use App\Exception\BadRequestException;
use App\Exception\DuplicateException;
use App\Exception\NotFoundException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AuthService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly MailService $mailService
    ) {
    }

    /**
     * @throws DuplicateException
     * @throws \Exception
     * @throws TransportExceptionInterface
     */
    public function registration(string $email, string $password, string $name, string $phone): Users
    {
        $userRepository = $this->entityManager->getRepository(Users::class);
        $user = $userRepository->findOneBy(['email' => $email]);
        if (null !== $user) {
            throw new DuplicateException();
        }

        $verificationCode = mt_rand(100000, 999999);

        $user = (new Users())
            ->setEmail($email)
            ->setName($name)
            ->setPhone($phone)
            ->setToken($verificationCode);
        $hashedPassword = $this->passwordHasher->hashPassword($user, $password);
        $user->setPassword($hashedPassword);

        $this->entityManager->persist($user);
        $this->entityManager->flush();
        $this->mailService->sendCode($email, $verificationCode, MailService::SUBJECT_VERIFICATION);

        return $user;
    }

    /**
     * @throws NotFoundException
     * @throws AccessDeniedException
     * @throws \Exception
     */
    public function verifyCode(string $email, int $token): Users
    {
        $user = $this->entityManager->getRepository(Users::class)->findOneBy(['email' => $email]);
        if (null === $user) {
            throw new NotFoundException();
        }

        if ($user->getToken() !== $token) {
            throw new AccessDeniedException();
        }
        if (true === $user->getVerified()) {
            throw new BadRequestException();
        }

        $wallet = (new Wallet())
            ->setUser($user);

        $user->setVerified(true);
        $this->entityManager->persist($wallet);
        $this->entityManager->flush();

        return $user;
    }

    /**
     * @throws NotFoundException
     * @throws \Exception
     * @throws TransportExceptionInterface
     */
    public function resetPassword(string $email): Users
    {
        $user = $this->entityManager->getRepository(Users::class)->findOneBy(['email' => $email]);
        if (null === $user) {
            throw new NotFoundException();
        }

        $token = mt_rand(100000, 999999);
        $user->setToken($token);

        $this->entityManager->persist($user);
        $this->entityManager->flush();
        $this->mailService->sendCode($email, $token, MailService::SUBJECT_RESET);

        return $user;
    }

    /**
     * @throws NotFoundException
     * @throws AccessDeniedException
     * @throws \Exception
     * @throws TransportExceptionInterface
     */
    public function reset(string $email, int $token, string $password): Users
    {
        $user = $this->entityManager->getRepository(Users::class)->findOneBy(['email' => $email]);
        if (null === $user) {
            throw new NotFoundException();
        }

        if ($user->getToken() !== $token) {
            throw new AccessDeniedException();
        }

        $hashedPassword = $this->passwordHasher->hashPassword($user, $password);
        $user->setPassword($hashedPassword);

        $user->setToken(null);

        $this->entityManager->persist($user);
        $this->entityManager->flush();
        $this->mailService->sendPasswordResetConfirmation($email);

        return $user;
    }
}
