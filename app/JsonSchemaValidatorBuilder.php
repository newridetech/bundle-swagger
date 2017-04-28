<?php

namespace Absolvent\swagger;

use JsonSchema\Constraints\Constraint;
use JsonSchema\Constraints\Factory;
use JsonSchema\Validator;

/**
 * Unfortunately JsonSchema\Validator is stateful. To overcome this we need
 * a builder.
 */
class JsonSchemaValidatorBuilder
{
    public $swaggerSchema;

    public function __construct(SwaggerSchema $swaggerSchema)
    {
        $this->swaggerSchema = $swaggerSchema;
    }

    public function createJsonSchemaFactory(): Factory
    {
        $schemaStorage = new SchemaStorage(null, null, $this->swaggerSchema);

        return new Factory($schemaStorage, null, Constraint::CHECK_MODE_COERCE_TYPES);
    }

    public function createJsonSchemaValidator(): Validator
    {
        $factory = $this->createJsonSchemaFactory();

        return new Validator($factory);
    }
}
