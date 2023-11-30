<?php

namespace App\Exceptions;

use Symfony\Component\HttpFoundation\Response;

class ForbiddenException extends Exception
{
    const HTTP_CODE = Response::HTTP_FORBIDDEN;
}
