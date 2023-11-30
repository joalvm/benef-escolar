<?php

namespace App\Exceptions;

use Symfony\Component\HttpFoundation\Response;

class UnauthorizedException extends Exception
{
    const HTTP_CODE = Response::HTTP_UNAUTHORIZED;
}
