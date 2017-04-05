<?php

namespace Absolvent\swagger;

abstract class SwaggerValidator
{
    abstract public function validateAgainst(SwaggerSchema $schema): SwaggerValidationResult;
}
