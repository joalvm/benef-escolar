<?php

namespace App\Exceptions;

use Symfony\Component\HttpFoundation\Response;

class NotAcceptableException extends Exception
{
    const HTTP_CODE = Response::HTTP_NOT_ACCEPTABLE;
}
