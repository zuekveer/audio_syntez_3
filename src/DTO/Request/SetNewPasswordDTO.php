<?php

declare(strict_types=1);

namespace App\DTO\Request;

use App\DTO\DTOResolvedInterface;
use Symfony\Component\Validator\Constraints as Assert;

class SetNewPasswordDTO implements DTOResolvedInterface
{
    #[Assert\NotBlank]
    #[Assert\Email]
    private string $email;

    #[Assert\NotBlank]
    #[Assert\Type('string')]
    #[Assert\Length(min: 6)]
    #[Assert\PasswordStrength(
        minScore: 3,
        message: 'Password must contain at least one uppercase letter, one lowercase letter, one number, one special character and length >= 10.'
    )]
    private string $password;

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

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

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
