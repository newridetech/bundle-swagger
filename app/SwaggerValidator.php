<?php

namespace Newride\swagger;

abstract class SwaggerValidator
{
    abstract public function validateAgainst(SwaggerSchema $schema): SwaggerValidationResult;
}
