<?php

namespace Absolvent\swagger\tests\Unit;

use Absolvent\swagger\SwaggerSchema;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SwaggerSchemaTest extends TestCase
{
    public function testThatSwaggerSchemaIsCreated(): SwaggerSchema
    {
        return SwaggerSchema::fromFilename(__DIR__.'/../../fixtures/petstore-expanded.yml');
    }

    /**
     * @depends testThatSwaggerSchemaIsCreated
     */
    public function testThatBasePathIsFetched(SwaggerSchema $swaggerSchema)
    {
        self::assertEquals('/api', $swaggerSchema->get('basePath'));
    }

    /**
     * @depends testThatSwaggerSchemaIsCreated
     */
    public function testThatRequestMethodSchemaIsFound(SwaggerSchema $swaggerSchema)
    {
        $request = Request::create('http://example.com/api/pets', 'GET');
        $schema = $swaggerSchema->findRequestMethodSchemaByHttpRequest($request);
        self::assertTrue($schema->has('description'));
        self::assertTrue($schema->has('operationId'));
        self::assertTrue($schema->has('responses'));
    }

    /**
     * @depends testThatSwaggerSchemaIsCreated
     */
    public function testThatRequestPathSchemaIsFound(SwaggerSchema $swaggerSchema)
    {
        $request = Request::create('http://example.com/api/pets', 'GET');
        $schema = $swaggerSchema->findRequestPathSchemaByHttpRequest($request);
        self::assertTrue($schema->has('get'));
    }

    /**
     * @depends testThatSwaggerSchemaIsCreated
     */
    public function testThatRequestParametersSchemaIsFound(SwaggerSchema $swaggerSchema)
    {
        $request = Request::create('http://example.com/api/pets', 'GET');
        $schema = $swaggerSchema->findRequestParametersSchemaByHttpRequest($request);

        self::assertTrue($schema->has(0));
        self::assertObjectHasAttribute('name', $schema->get(0));
        self::assertObjectHasAttribute('description', $schema->get(0));
    }

    /**
     * @depends testThatSwaggerSchemaIsCreated
     */
    public function testThatResponseSchemaIsFound(SwaggerSchema $swaggerSchema)
    {
        $request = Request::create('http://example.com/api/pets', 'GET');
        $response = Response::create(json_encode([
            [
                'pet_id' => 1,
                'pet_name' => 'test',
            ],
        ]), 200);

        $schema = $swaggerSchema->findResponseSchemaByHttpResponse($request, $response);
        self::assertTrue($schema->has('type'));
        self::assertTrue($schema->has('items'));
    }

    /**
     * @depends testThatSwaggerSchemaIsCreated
     * @expectedException \Absolvent\swagger\Exception\SchemaPartNotFound
     */
    public function testThatMissingPathIsHandled(SwaggerSchema $swaggerSchema)
    {
        $request = Request::create('http://example.com/api/animal', 'GET');
        $response = Response::create('', 200);
        $swaggerSchema->findResponseSchemaByHttpResponse($request, $response);
    }

    /**
     * @depends testThatSwaggerSchemaIsCreated
     * @expectedException \Absolvent\swagger\Exception\SchemaPartNotFound
     */
    public function testThatMissingMethodIsHandled(SwaggerSchema $swaggerSchema)
    {
        $request = Request::create('http://example.com/api/pets', 'HEAD');
        $response = Response::create('', 200);
        $swaggerSchema->findResponseSchemaByHttpResponse($request, $response);
    }

    /**
     * @depends testThatSwaggerSchemaIsCreated
     * @expectedException \Absolvent\swagger\Exception\SchemaPartNotFound
     */
    public function testThatMissingParametersIsHandled(SwaggerSchema $swaggerSchema)
    {
        $request = Request::create('http://example.com/api/pet', 'GET');
        $swaggerSchema->findRequestParametersSchemaByHttpRequest($request);
    }

    /**
     * @depends testThatSwaggerSchemaIsCreated
     * @expectedException \Absolvent\swagger\Exception\SchemaPartNotFound
     */
    public function testThatMissingStatusCodeIsHandled(SwaggerSchema $swaggerSchema)
    {
        $request = Request::create('http://example.com/api/pet', 'GET');
        $response = Response::create('', 123);
        $swaggerSchema->findResponseSchemaByHttpResponse($request, $response);
    }
}
