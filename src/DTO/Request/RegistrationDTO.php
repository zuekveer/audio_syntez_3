<?php

declare(strict_types=1);

namespace App\DTO\Request;

use App\DTO\DTOResolvedInterface;
use Symfony\Component\Validator\Constraints as Assert;

class RegistrationDTO implements DTOResolvedInterface
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

    #[Assert\NotBlank]
    #[Assert\Type('string')]
    private string $name;

    #[Assert\NotBlank]
    #[Assert\Type('string')]
    #[Assert\Length(exactly: 10, exactMessage: 'your phone number should contain only 10 digits')]
    private string $phone;

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

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }
}
