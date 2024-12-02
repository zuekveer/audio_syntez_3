<?php

declare(strict_types=1);

namespace App\Services\Voice;

use App\Entity\Voice;
use Doctrine\ORM\EntityManagerInterface;

class VoiceService
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
    }

    public function getAllVoices(): array
    {
        $voiceRepository = $this->entityManager->getRepository(Voice::class);
        return $voiceRepository->findAll();
    }

    public function createVoice(string $name, float $price, ?array $parameters = null): Voice
    {
        $voice = new Voice();
        $voice->setName($name)
            ->setPricePerCharacter($price)
            ->setParameters($parameters);

        $this->entityManager->persist($voice);
        $this->entityManager->flush();

        return $voice;
    }
}
