<?php

namespace Absolvent\swagger\tests\Unit;

use Absolvent\swagger\JsonSchema\RequestParameters as RequestParametersSchema;
use Absolvent\swagger\RequestParameters;
use Absolvent\swagger\SwaggerSchema;
use Absolvent\swagger\tests\TestCase;
use Symfony\Component\HttpFoundation\Request;

class RequestParametersTest extends TestCase
{
    private static $expectedLimit = 25;
    private static $expectedTags = [
        'hiho',
    ];

    public function testThatRequestIsCreated(): Request
    {
        return Request::create('http://example.com/api/pets', 'GET', [
            'limit' => self::$expectedLimit,
            'tags' => self::$expectedTags,
        ]);
    }

    public function testThatSwaggerSchemaIsCreated(): SwaggerSchema
    {
        return SwaggerSchema::fromFilename(base_path('fixtures/petstore-expanded.yml'));
    }

    /**
     * @depends testThatRequestIsCreated
     * @depends testThatSwaggerSchemaIsCreated
     */
    public function testThatRequestParametersSchemaIsObtained(Request $request, SwaggerSchema $swaggerSchema): RequestParametersSchema
    {
        return $swaggerSchema->findRequestParametersSchemaByHttpRequest($request);
    }

    /**
     * @depends testThatRequestIsCreated
     * @depends testThatRequestParametersSchemaIsObtained
     */
    public function testThatRequestDataIsObtained(Request $request, RequestParametersSchema $requestParametersSchema)
    {
        $requestParameters = new RequestParameters($request);
        $data = $requestParameters->getDataByRequestParametersSchema($requestParametersSchema);

        $this->assertObjectHasAttribute('limit', $data);
        $this->assertEquals(self::$expectedLimit, $data->limit);

        $this->assertObjectHasAttribute('tags', $data);
        $this->assertEquals(self::$expectedTags, $data->tags);
    }
}