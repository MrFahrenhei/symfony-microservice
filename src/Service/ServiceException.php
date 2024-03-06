<?php

namespace App\Service;

use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class ServiceException extends HttpException
{
    private ServiceExceptionData $exceptionData;
    public function __construct(ServiceExceptionData $exceptionData)
    {
        $statusCode = $exceptionData->getStatusCode();
        $message = $exceptionData->getType();
        parent::__construct($statusCode, $message);
        $this->exceptionData = $exceptionData;
    }

    public function getExceptionData(): ServiceExceptionData
    {
       return $this->exceptionData;
    }

}