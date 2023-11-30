<?php

namespace App\Exceptions;

use Symfony\Component\HttpFoundation\Response;

class BadRequestException extends Exception
{
    const HTTP_CODE = Response::HTTP_BAD_REQUEST;
}
