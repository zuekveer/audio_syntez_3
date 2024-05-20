<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Exception\CustomExceptionInterface;
use App\Exception\ValidationException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ExceptionEventListener
{
    public function __construct(private readonly LoggerInterface $logger)
    {
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        switch (true) {
            case $exception instanceof CustomExceptionInterface:
                $response = new JsonResponse($exception->getMessage(), $exception->getCode());
                $this->logger->info('An exception occurred in CustomExceptionInterface scenario', ['exception' => $exception]);
                $event->setResponse($response);
                break;
            case $exception instanceof ValidationException:
                $response = new JsonResponse([
                    'errors' => $this->formatErrors($exception->getViolationList()),
                ]);
                $this->logger->info('An exception occurred in ValidationException scenario', ['exception' => $exception]);
                $event->setResponse($response);
                break;
            case $exception instanceof HttpExceptionInterface:
                $response = new JsonResponse($exception->getMessage(), $exception->getStatusCode());
                $this->logger->info('HttpException', ['exception' => $exception]);
                break;
            default:
                $response = new JsonResponse('An exception occurred', Response::HTTP_INTERNAL_SERVER_ERROR);
                $this->logger->error('An exception occurred in Default scenario', ['exception' => $exception]);
                break;
        }

        $event->setResponse($response);
    }

    public function formatErrors(ConstraintViolationListInterface $violationList): array
    {
        $errors = [];

        /** @var ConstraintViolation $violation */
        foreach ($violationList as $violation) {
            $data = [
                'message' => $violation->getMessage(),
                'code' => $violation->getCode(),
                'context' => [
                    'field' => $violation->getPropertyPath(),
                ],
            ];

            $errors[] = $data;
        }

        return $errors;
    }
}
