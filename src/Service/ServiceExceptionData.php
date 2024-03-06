<?php

namespace App\Service;

use phpDocumentor\Reflection\Types\Parent_;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ServiceExceptionData extends HttpException
{
    public function __construct(protected int $statusCode, protected string $type)
    {
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function toArray(): array
    {
        return [
            'type'=>$this->type,
        ];
    }
}