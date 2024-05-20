<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\DTO\Request\RegistrationDTO;
use App\DTO\Request\ResetPasswordDTO;
use App\DTO\Request\SetNewPasswordDTO;
use App\DTO\Request\VerifyCodeDTO;
use App\Services\AuthService;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class AuthController extends AbstractController
{
    public function __construct(
        private readonly AuthService $authService,
    ) {
    }

    #[Route('/api/registration', methods: ['POST'])]
    #[OA\RequestBody(
        description: 'User credentials',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: RegistrationDTO::class))
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Registration successful'
    )]
    #[OA\Response(
        response: 409,
        description: 'User already registered'
    )]
    #[OA\Parameter(
        name: 'registration',
        description: 'responses',
        in: 'query', // should I change it?
        schema: new OA\Schema(type: RegistrationDTO::class), // should I change it too?
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: RegistrationDTO::class))
        )
    )]
    #[OA\Tag(name: 'Authentication')]
    #[Security(name: 'Bearer')]
    public function registration(RegistrationDTO $dto): JsonResponse
    {
        $this->authService
            ->registration(
                $dto->getEmail(),
                $dto->getPassword(),
                $dto->getName(),
                $dto->getPhone());

        return new JsonResponse(['success' => 'User has been registered. Verification code sent'], 200);
    }

    #[Route('/api/verify-code', methods: ['POST'])]
    #[OA\RequestBody(
        description: 'User credentials',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: VerifyCodeDTO::class))
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Email verification success'
    )]
    #[OA\Response(
        response: 400,
        description: 'Bad Request'
    )]
    #[OA\Response(
        response: 403,
        description: 'Access denied'
    )]
    #[OA\Parameter(
        name: 'email verification',
        description: 'responses',
        in: 'query',
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\Tag(name: 'Authentication')]
    #[Security(name: 'Bearer')]
    public function verifyCode(VerifyCodeDTO $dto): JsonResponse
    {
        $this->authService->verifyCode($dto->getEmail(), $dto->getToken())->getVerified();

        return new JsonResponse(['success' => 'Verification successful'], 200);
    }

    #[Route('/api/reset', methods: ['POST'])]
    #[OA\RequestBody(
        description: 'User credentials',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: ResetPasswordDTO::class))
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Password was reset'
    )]
    #[OA\Response(
        response: 404,
        description: 'User not found'
    )]
    #[OA\Parameter(
        name: 'password reset',
        description: 'responses',
        in: 'query',
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\Tag(name: 'Authentication')]
    #[Security(name: 'Bearer')]
    public function resetPassword(ResetPasswordDTO $dto): JsonResponse
    {
        $this->authService->resetPassword($dto->getEmail());

        return new JsonResponse(['success' => 'Reset code sent'], 200);
    }

    #[Route('/api/reset', methods: ['PUT'])]
    #[OA\RequestBody(
        description: 'User credentials',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: SetNewPasswordDTO::class))
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Returns the rewards of an user'
    )]
    #[OA\Response(
        response: 404,
        description: 'User not found'
    )]
    #[OA\Response(
        response: 403,
        description: 'Access denied'
    )]
    #[OA\Parameter(
        name: 'set a new password',
        description: 'responses',
        in: 'query',
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\Tag(name: 'Authentication')]
    #[Security(name: 'Bearer')]
    public function reset(SetNewPasswordDTO $dto): JsonResponse
    {
        $this->authService->reset($dto->getEmail(), $dto->getToken(), $dto->getPassword());

        return new JsonResponse(['message' => 'Password reset successfully'], 200);
    }
}
