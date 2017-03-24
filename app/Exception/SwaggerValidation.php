<?php

namespace Absolvent\swagger\Exception;

use Throwable;
use UnexpectedValueException;

class SwaggerValidation extends UnexpectedValueException
{
    public function __construct(array $errors, int $code = 0, Throwable $previous = null)
    {
        $message = json_encode($errors, JSON_PRETTY_PRINT);

        parent::__construct($message, $code, $previous);
    }
}
