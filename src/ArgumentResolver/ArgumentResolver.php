<?php

declare(strict_types=1);

namespace App\ArgumentResolver;

use App\DtoModel\BaseDtoModelInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ArgumentResolver implements ArgumentValueResolverInterface
{
    /**
     * @var ValidatorInterface
     */
    private ValidatorInterface $validator;

    /**
     * @param ValidatorInterface $validator
     */
    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @param Request $request
     * @param ArgumentMetadata $argument
     * @return bool
     */
    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        try {
            $reflection = new \ReflectionClass($argument->getType());
            if ($reflection->implementsInterface(BaseDtoModelInterface::class)) {
                return true;
            }
            return false;
        } catch (\ReflectionException $exception) {
            return false;
        }
    }

    /**
     * @param Request $request
     * @param ArgumentMetadata $argument
     * @return \Generator
     */
    public function resolve(Request $request, ArgumentMetadata $argument): \Generator
    {
        $class = $argument->getType();
        $dto = new $class($request);

        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            throw new BadRequestHttpException((string)$errors);
        }

        yield $dto;
    }
}