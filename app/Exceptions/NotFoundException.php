<?php

namespace App\Exceptions;

use Symfony\Component\HttpFoundation\Response;

class NotFoundException extends Exception
{
    const HTTP_CODE = Response::HTTP_NOT_FOUND;
}
