<?php

namespace App\Exceptions;

use Exception as BaseException;
use Symfony\Component\HttpFoundation\Response;

abstract class Exception extends BaseException
{
    public const HTTP_CODE = 500;

    protected $errors = null;
    protected $message = null;
    protected $httpCode = 500;

    public function __construct(...$args)
    {
        $this->httpCode = static::HTTP_CODE ?? 500;

        $this->normalizeArgs($args);

        if (empty($this->message)) {
            $this->message = Response::$statusTexts[$this->httpCode];
        }

        parent::__construct($this->message, $this->httpCode);
    }

    private function normalizeArgs($args)
    {
        foreach ($args as $arg) {
            if (is_string($arg)) {
                if (empty(trim($arg))) {
                    $this->message = Response::$statusTexts[$this->httpCode];
                } else {
                    $this->message = trim($arg);
                }
            } elseif (is_array($arg) || is_object($arg)) {
                $this->errors = $arg;
            }
        }
    }

    public function errors()
    {
        return $this->errors;
    }
}
