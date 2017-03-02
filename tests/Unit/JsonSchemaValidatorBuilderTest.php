<?php

namespace Absolvent\swagger\tests\Unit;

use Absolvent\swagger\JsonSchemaValidatorBuilder;
use Absolvent\swagger\SwaggerSchema;
use Absolvent\swagger\tests\TestCase;
use JsonSchema\Validator;

class JsonSchemaValidatorBuilderTest extends TestCase
{
    public function testThatSchemaIsCreatedFromFilename(): SwaggerSchema
    {
        return SwaggerSchema::fromFilename(base_path('fixtures/petstore.yml'));
    }

    /**
     * @depends testThatSchemaIsCreatedFromFilename
     */
    public function testThatJsonSchemaValidatorBuilderCreatesValidator(SwaggerSchema $swaggerSchema)
    {
        $builder = new JsonSchemaValidatorBuilder($swaggerSchema);
        $validator = $builder->createJsonSchemaValidator();
        $this->assertInstanceOf(Validator::class, $validator);
    }
}
