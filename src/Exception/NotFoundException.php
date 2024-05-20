<?php

declare(strict_types=1);

namespace App\Exception;

class NotFoundException extends \Exception implements CustomExceptionInterface
{
    public function __construct(string $message = 'Not found', int $code = 404, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
