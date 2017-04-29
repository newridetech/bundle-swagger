<?php

namespace Absolvent\swagger\tests\Unit;

use Absolvent\swagger\JsonSchemaValidatorBuilder;
use Absolvent\swagger\SwaggerSchema;
use JsonSchema\Validator;
use PHPUnit\Framework\TestCase;

class JsonSchemaValidatorBuilderTest extends TestCase
{
    public function testThatSchemaIsCreatedFromFilename(): SwaggerSchema
    {
        return SwaggerSchema::fromFilename(__DIR__.'/../../fixtures/petstore.yml');
    }

    /**
     * @depends testThatSchemaIsCreatedFromFilename
     */
    public function testThatJsonSchemaValidatorBuilderCreatesValidator(SwaggerSchema $swaggerSchema)
    {
        $builder = new JsonSchemaValidatorBuilder($swaggerSchema);
        $validator = $builder->createJsonSchemaValidator();
        self::assertInstanceOf(Validator::class, $validator);
    }
}
