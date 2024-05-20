<?php

declare(strict_types=1);

namespace App\DTO\Request;

use App\DTO\DTOResolvedInterface;
use Symfony\Component\Validator\Constraints as Assert;

class ResetPasswordDTO implements DTOResolvedInterface
{
    #[Assert\NotBlank]
    #[Assert\Email]
    private string $email;

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }
}
