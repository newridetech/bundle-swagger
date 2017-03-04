<?php

namespace Absolvent\swagger;

use JsonSchema\Validator;

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

    public function isValid(): bool
    {
        return $this->validator->isValid();
    }
}
