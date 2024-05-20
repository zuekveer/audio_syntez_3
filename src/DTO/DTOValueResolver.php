<?php

declare(strict_types=1);

namespace App\DTO;

use App\Exception\ValidationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class DTOValueResolver implements ValueResolverInterface
{
    public function __construct(
        private readonly ValidatorInterface $validator,
        private readonly DenormalizerInterface $denormalizer,
    ) {
    }

    /**
     * @throws ExceptionInterface
     */
    public function resolve(Request $request, ArgumentMetadata $argument): \Generator
    {
        if (!$argument->getType() || !is_subclass_of($argument->getType(), DTOResolvedInterface::class)) {
            return [];
        }
        $headers = $request->headers->all();
        $headers = array_combine(
            array_map(fn ($name) => str_replace('-', '_', $name), array_keys($headers)),
            array_map(fn ($value) => is_array($value) ? reset($value) : $value, $headers)
        );

        $content = $request->getContent();
        $contentDecoded = json_decode($content, true) ?? [];
        $payload = array_merge($request->request->all(), $request->query->all(), $request->files->all(), $headers, $contentDecoded);
        $request = $this->denormalizer->denormalize($payload, $argument->getType(), null, [
            AbstractObjectNormalizer::DISABLE_TYPE_ENFORCEMENT => true,
        ]);

        $violations = $this->validator->validate($request);

        if ($violations->count()) {
            throw new ValidationException($violations);
        }

        yield $request;
    }
}
