<?php

declare(strict_types=1);

namespace App\DTO\Request;

use App\DTO\DTOResolvedInterface;
use Symfony\Component\Validator\Constraints as Assert;

class VerifyCodeDTO implements DTOResolvedInterface
{
    #[Assert\NotBlank]
    #[Assert\Email]
    private string $email;

    #[Assert\Type('int')]
    #[Assert\Length(exactly: 6)]
    private int $token;

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getToken(): int
    {
        return $this->token;
    }

    public function setToken(int $token): self
    {
        $this->token = $token;

        return $this;
    }
}
