<?php

declare(strict_types=1);

namespace App\DTO\Request;

use App\DTO\DTOResolvedInterface;
use Symfony\Component\Validator\Constraints as Assert;

class ChangeBalanceDTO implements DTOResolvedInterface
{
    #[Assert\Type('int')]
    #[Assert\NotBlank]
    private int $balance;

    public function getBalance(): int
    {
        return $this->balance;
    }

    public function setBalance(int $balance): self
    {
        $this->balance = $balance;

        return $this;
    }
}
