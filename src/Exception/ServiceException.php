<?php

namespace App\Exception;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ServiceException extends HttpException
{
    /**
     * @var array $errors
     */
    private array $errors;

    /**
     * ApiException constructor.
     * @param \Throwable $exception
     */
    public function __construct(\Throwable $exception)
    {
        parent::__construct(
            $exception->getCode() ?: Response::HTTP_BAD_REQUEST,
            $exception->getMessage(),
            $exception
        );
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}