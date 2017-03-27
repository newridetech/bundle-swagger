<?php

namespace Absolvent\swagger\tests\Unit;

use Absolvent\swagger\SwaggerSchema;
use Absolvent\swagger\tests\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SwaggerSchemaTest extends TestCase
{
    public function testThatSwaggerSchemaIsCreated(): SwaggerSchema
    {
        return SwaggerSchema::fromFilename(base_path('fixtures/petstore-expanded.yml'));
    }

    /**
     * @depends testThatSwaggerSchemaIsCreated
     */
    public function testThatBasePathIsFetched(SwaggerSchema $swaggerSchema)
    {
        $this->assertEquals('/api', $swaggerSchema->get('basePath'));
    }

    /**
     * @depends testThatSwaggerSchemaIsCreated
     */
    public function testThatRequestMethodSchemaIsFound(SwaggerSchema $swaggerSchema)
    {
        $request = Request::create('http://example.com/api/pets', 'GET');
        $schema = $swaggerSchema->findRequestMethodSchemaByHttpRequest($request);
        $this->assertObjectHasAttribute('description', $schema);
        $this->assertObjectHasAttribute('operationId', $schema);
        $this->assertObjectHasAttribute('responses', $schema);
    }

    /**
     * @depends testThatSwaggerSchemaIsCreated
     */
    public function testThatRequestPathSchemaIsFound(SwaggerSchema $swaggerSchema)
    {
        $request = Request::create('http://example.com/api/pets', 'GET');
        $schema = $swaggerSchema->findRequestPathSchemaByHttpRequest($request);
        $this->assertObjectHasAttribute('get', $schema);
    }

    /**
     * @depends testThatSwaggerSchemaIsCreated
     */
    public function testThatRequestParametersSchemaIsFound(SwaggerSchema $swaggerSchema)
    {
        $request = Request::create('http://example.com/api/pets', 'GET');
        $schema = $swaggerSchema->findRequestParametersSchemaByHttpRequest($request);

        $this->assertArrayHasKey(0, $schema);
        $this->assertObjectHasAttribute('name', $schema[0]);
        $this->assertObjectHasAttribute('description', $schema[0]);
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
        $this->assertObjectHasAttribute('type', $schema);
        $this->assertObjectHasAttribute('items', $schema);
    }

    /**
     * @depends testThatSwaggerSchemaIsCreated
     * @expectedException \Absolvent\swagger\Exception\SchemaPartNotFound\Path
     */
    public function testThatMissingPathIsHandled(SwaggerSchema $swaggerSchema)
    {
        $request = Request::create('http://example.com/api/animal', 'GET');
        $response = Response::create('', 200);
        $swaggerSchema->findResponseSchemaByHttpResponse($request, $response);
    }

    /**
     * @depends testThatSwaggerSchemaIsCreated
     * @expectedException \Absolvent\swagger\Exception\SchemaPartNotFound\Method
     */
    public function testThatMissingMethodIsHandled(SwaggerSchema $swaggerSchema)
    {
        $request = Request::create('http://example.com/api/pets', 'HEAD');
        $response = Response::create('', 200);
        $swaggerSchema->findResponseSchemaByHttpResponse($request, $response);
    }

    /**
     * @depends testThatSwaggerSchemaIsCreated
     * @expectedException \Absolvent\swagger\Exception\SchemaPartNotFound\Parameters
     */
    public function testThatMissingParametersIsHandled(SwaggerSchema $swaggerSchema)
    {
        $request = Request::create('http://example.com/api/pet', 'GET');
        $swaggerSchema->findRequestParametersSchemaByHttpRequest($request);
    }

    /**
     * @depends testThatSwaggerSchemaIsCreated
     * @expectedException \Absolvent\swagger\Exception\SchemaPartNotFound\StatusCode
     */
    public function testThatMissingStatusCodeIsHandled(SwaggerSchema $swaggerSchema)
    {
        $request = Request::create('http://example.com/api/pets', 'GET');
        $response = Response::create('', 123);
        $swaggerSchema->findResponseSchemaByHttpResponse($request, $response);
    }
}
