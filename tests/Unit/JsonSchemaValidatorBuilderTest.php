<?php

namespace Newride\swagger\tests\Unit;

use Newride\swagger\JsonSchemaValidatorBuilder;
use Newride\swagger\SwaggerSchema;
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
