<?php

namespace Absolvent\swagger;

use JsonSchema\Constraints\Factory;
use JsonSchema\SchemaStorage;
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

    public function createJsonSchemaStorage(): SchemaStorage
    {
        $schemaStorage = new SchemaStorage();
        $schemaStorage->addSchema(
            'file://definitions',
            $this->swaggerSchema->schema->definitions
        );

        return $schemaStorage;
    }

    public function createJsonSchemaFactory(): Factory
    {
        $schemaStorage = $this->createJsonSchemaStorage();

        return new Factory($schemaStorage);
    }

    public function createJsonSchemaValidator(): Validator
    {
        $factory = $this->createJsonSchemaFactory();

        return new Validator($factory);
    }
}
