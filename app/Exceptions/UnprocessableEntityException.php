<?php

namespace App\Exceptions;

use Symfony\Component\HttpFoundation\Response;

class UnprocessableEntityException extends Exception
{
    const HTTP_CODE = Response::HTTP_UNPROCESSABLE_ENTITY;
}
