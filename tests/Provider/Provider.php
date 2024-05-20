<?php

declare(strict_types=1);

namespace App\Tests\Provider;

use App\Entity\Wallet;
use App\Exception\AccessDeniedException;
use App\Exception\NotEnoughMoneyException;
use App\Exception\NotFoundException;
use Symfony\Component\HttpFoundation\Response;

final class Provider
{
    public static function registrationProvider(): array
    {
        return [
            ['name', 'test@test.com', '+0123456789', 'TestUserPass01234!', Response::HTTP_OK],
            ['name', 'test@test.com', '+0123456789', 'TestUserPass01234!', Response::HTTP_CONFLICT],
            ['na', 'testtest.cm', '+0123456789', 'TestUserPass01234!', Response::HTTP_BAD_REQUEST],
        ];
    }

    public static function verifyCodeProvider(): array
    {
        return [
            ['test@test.com', Response::HTTP_OK],
            ['tes@test.com', Response::HTTP_NOT_FOUND],
            ['na', Response::HTTP_BAD_REQUEST],
        ];
    }

    public static function resetPasswordProvider(): array
    {
        return [
            ['test@test.com', Response::HTTP_OK],
            ['tes@test.com', Response::HTTP_NOT_FOUND],
            ['na', Response::HTTP_BAD_REQUEST],
        ];
    }

    public static function setNewPasswordProvider(): array
    {
        return [
            ['test@test.com', Response::HTTP_OK],
            ['tes@test.com', Response::HTTP_NOT_FOUND],
            ['na', Response::HTTP_BAD_REQUEST],
        ];
    }

    public static function loginProvider(): array
    {
        return [
            ['test@test.com', 'TestUserPass01234!', Response::HTTP_OK],
            ['tes@test.com', 'TestUserPass01234!', Response::HTTP_UNAUTHORIZED],
        ];
    }

    public static function getBalanceProvider(): array
    {
        return [
            'user with no wallet' => [null, NotFoundException::class],
            'user with wallet' => [new Wallet(), null],
        ];
    }

    public static function changeBalanceProvider(): array
    {
        return [
            'user with no wallet' => [null, NotFoundException::class, null, null, null, 0, 0, 0],
            'user with wallet' => [new Wallet(), null, null, null, null, 0, 0, 0],
            'role customer positive count' => [new Wallet(), null, AccessDeniedException::class, null, true, 100, -100, 0],
            'role admin positive count' => [new Wallet(), null, null, null, false, 0, 100, 100],
            'not enough money' => [new Wallet(), null, null, NotEnoughMoneyException::class, null, -100, 0, -100],
        ];
    }

    public static function putProvider(): array
    {
        return [
            ['test@test.com', 1, Response::HTTP_OK],
            ['test@test.com', -2, Response::HTTP_PAYMENT_REQUIRED],
            ['tes@test.com', 1, Response::HTTP_NOT_FOUND],
            ['na', 0, Response::HTTP_BAD_REQUEST],
        ];
    }

    public static function getProvider(): array
    {
        return [
            ['test@test.com', Response::HTTP_OK],
            ['tes@test.com', Response::HTTP_NOT_FOUND],
        ];
    }
}
