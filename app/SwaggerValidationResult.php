<?php

namespace Absolvent\swagger;

use Absolvent\swagger\Exception\SwaggerValidation as SwaggerValidationException;
use JsonSchema\Validator;
use Throwable;

class SwaggerValidationResult
{
    protected $validator;

    public function __construct(Validator $validator)
    {
        $this->validator = $validator;
    }

    public function getErrors(): array
    {
        return $this->validator->getErrors();
    }

    public function getException(int $code = 0, Throwable $previous = null): SwaggerValidationException
    {
        return new SwaggerValidationException($this->getErrors(), $code, $previous);
    }

    public function isValid(): bool
    {
        return $this->validator->isValid();
    }
}
