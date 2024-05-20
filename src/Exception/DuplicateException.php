<?php

declare(strict_types=1);

namespace App\Exception;

class DuplicateException extends \Exception implements CustomExceptionInterface
{
    public function __construct(string $message = 'Duplicate', int $code = 409, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
