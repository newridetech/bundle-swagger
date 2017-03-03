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
        return SwaggerSchema::fromFilename(base_path('fixtures/petstore.yml'));
    }

    /**
     * @depends testThatSwaggerSchemaIsCreated
     */
    public function testThatRequestMethodSchemaIsFound(SwaggerSchema $swaggerSchema)
    {
        $request = Request::create('http://example.com/pet', 'GET');
        $schema = $swaggerSchema->findRequestMethodSchemaByHttpRequest($request);
        $this->assertObjectHasAttribute('description', $schema);
        $this->assertObjectHasAttribute('produces', $schema);
        $this->assertObjectHasAttribute('responses', $schema);
    }

    /**
     * @depends testThatSwaggerSchemaIsCreated
     */
    public function testThatRequestPathSchemaIsFound(SwaggerSchema $swaggerSchema)
    {
        $request = Request::create('http://example.com/pet', 'GET');
        $schema = $swaggerSchema->findRequestPathSchemaByHttpRequest($request);
        $this->assertObjectHasAttribute('get', $schema);
    }

    /**
     * @depends testThatSwaggerSchemaIsCreated
     */
    public function findResponseSchemaByHttpResponse(SwaggerSchema $swaggerSchema)
    {
        $request = Request::create('http://example.com/pet', 'GET');
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
}