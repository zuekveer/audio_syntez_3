<?php

declare(strict_types=1);

namespace App\Exception;

class NotEnoughMoneyException extends \Exception implements CustomExceptionInterface
{
    public function __construct(string $message = 'Not enough money for transaction', int $code = 400, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
