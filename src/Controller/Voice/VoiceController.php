<?php

declare(strict_types=1);

namespace App\Controller\Voice;

use App\Services\Voice\VoiceService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class VoiceController extends AbstractController
{
    public function __construct(
        private VoiceService $voiceService
    ) {
    }

    #[Route('/api/voices', name: 'get_voices', methods: ['GET'])]
    public function getAllVoices(): JsonResponse
    {
        $voices = $this->voiceService->getAllVoices();
        $response = array_map(fn($voice) => [
            'id' => $voice->getId(),
            'name' => $voice->getName(),
            'pricePerCharacter' => $voice->getPricePerCharacter(),
            'parameters' => $voice->getParameters(),
        ], $voices);

        return new JsonResponse($response, 200);
    }

    #[Route('/api/voices', name: 'create_voice', methods: ['POST'])]
    public function createVoice(Request $request): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $data = json_decode($request->getContent(), true);
        $voice = $this->voiceService->createVoice(
            $data['name'],
            $data['pricePerCharacter'],
            $data['parameters'] ?? null
        );

        return new JsonResponse([
            'id' => $voice->getId(),
            'name' => $voice->getName(),
            'pricePerCharacter' => $voice->getPricePerCharacter(),
            'parameters' => $voice->getParameters(),
        ], 201);
    }
}
