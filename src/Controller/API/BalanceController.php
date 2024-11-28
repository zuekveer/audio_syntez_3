<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\DTO\Request\ChangeBalanceDTO;
use App\Services\Balance\BalanceService;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class BalanceController extends AbstractController
{
    public function __construct(
        private readonly BalanceService $balanceService,
    ) {
    }

    #[Route('/api/balance', methods: ['GET'])]
    #[OA\RequestBody(
        description: 'User credentials',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: ChangeBalanceDTO::class))
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'current balance'
    )]
    #[OA\Parameter(
        name: 'balance',
        description: 'response',
        in: 'query',
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\Tag(name: 'Balance')]
    #[Security(name: 'Bearer')]
    public function getBalance(): JsonResponse
    {
        $wallet = $this->balanceService->getBalance();

        $balance = $wallet->getBalance();

        return new JsonResponse($balance, 200);
    }

    #[Route('/api/balance', methods: ['PUT'])]
    #[OA\RequestBody(
        description: 'User credentials',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: ChangeBalanceDTO::class))
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'change the balance'
    )]
    #[OA\Response(
        response: 400,
        description: 'bad request'
    )]
    #[OA\Response(
        response: 403,
        description: 'access denied'
    )]
    #[OA\Response(
        response: 404,
        description: 'balance not found'
    )]
    #[OA\Parameter(
        name: 'balance',
        description: 'responses',
        in: 'query',
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\Tag(name: 'Balance')]
    #[Security(name: 'Bearer')]
    public function changeBalance(ChangeBalanceDTO $dto): JsonResponse
    {
        $wallet = $this->balanceService->changeBalance($dto->getBalance());

        $balance = $wallet->getBalance();

        return new JsonResponse($balance, 200);
    }
}
