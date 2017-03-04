<?php

namespace Absolvent\swagger;

use JsonSchema\SchemaStorage;

class SchemaStorageBuilder
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
            $this->swaggerSchema->get('definitions')
        );

        return $schemaStorage;
    }
}
