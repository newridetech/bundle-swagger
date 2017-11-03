<?php

namespace Newride\swagger;

use Newride\swagger\Exception\SwaggerValidation as SwaggerValidationException;
use JsonSchema\Validator;
use Throwable;

class SwaggerValidationResult
{
    protected $errors;
    protected $isValid;

    public static function empty()
    {
        return new static([
            'errors' => [],
            'isValid' => true,
        ]);
    }

    public static function fromJsonSchemaValidator(Validator $validator): SwaggerValidationResult
    {
        return static::empty()->appendFromJsonSchemaValidator($validator);
    }

    public static function fromJsonSchemaValidatorList(array $jsonSchemaValidatorList): SwaggerValidationResult
    {
        return array_reduce($jsonSchemaValidatorList, function (SwaggerValidationResult $carry, Validator $jsonSchemaValidator) {
            return $carry->appendFromJsonSchemaValidator($jsonSchemaValidator);
        }, static::empty());
    }

    public function __construct(array $validationResult)
    {
        $this->errors = $validationResult['errors'];
        $this->isValid = $validationResult['isValid'];
    }

    public function appendFromJsonSchemaValidator(Validator $validator): SwaggerValidationResult
    {
        $next = clone $this;
        $errors = $validator->getErrors();
        if (!empty($errors)) {
            array_push($next->errors, ...$errors);
        }
        if (!$validator->isValid()) {
            $next->isValid = false;
        }

        return $next;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getException(int $code = 0, Throwable $previous = null): SwaggerValidationException
    {
        return new SwaggerValidationException($this->getErrors(), $code, $previous);
    }

    public function isValid(): bool
    {
        return $this->isValid;
    }
}
