<?php

namespace Absolvent\swagger\tests\Unit;

use Absolvent\swagger\RequestParameters;
use Absolvent\swagger\SwaggerSchema;
use Absolvent\swagger\tests\TestCase;
use Symfony\Component\HttpFoundation\Request;

class RequestParametersTest extends TestCase
{
    public function testThatRequestIsCreated(): Request
    {
        return Request::create('http://example.com/api/pets', 'GET');
    }

    public function testThatSwaggerSchemaIsCreated(): SwaggerSchema
    {
        return SwaggerSchema::fromFilename(base_path('fixtures/petstore-expanded.yml'));
    }

    /**
     * @depends testThatRequestIsCreated
     * @depends testThatSwaggerSchemaIsCreated
     */
    public function testThatRequestParametersSchemaIsObtained(Request $request, SwaggerSchema $swaggerSchema): array
    {
        return $swaggerSchema->findRequestParametersSchemaByHttpRequest($request);
    }

    /**
     * @depends testThatRequestIsCreated
     * @depends testThatRequestParametersSchemaIsObtained
     */
    public function testThatRequestDataIsObtained(Request $request, array $requestParametersSchema)
    {
        $requestParameters = new RequestParameters($request);
        $data = $requestParameters->getDataByRequestParametersSchema($requestParametersSchema);
        dd($data);
    }
}
