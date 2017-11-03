<?php

namespace Newride\swagger\tests\Unit;

use Newride\swagger\JsonSchema\RequestParameters as RequestParametersSchema;
use Newride\swagger\RequestParameters;
use Newride\swagger\SwaggerSchema;
use PHPUnit\Framework\TestCase;
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

    public function testThatPetstoreExpandedSwaggerSchemaIsCreated(): SwaggerSchema
    {
        return SwaggerSchema::fromFilename(__DIR__.'/../../fixtures/petstore-expanded.yml');
    }

    public function testThatPetstoreSwaggerSchemaIsCreated(): SwaggerSchema
    {
        return SwaggerSchema::fromFilename(__DIR__.'/../../fixtures/petstore.yml');
    }

    /**
     * @depends testThatRequestIsCreated
     * @depends testThatPetstoreExpandedSwaggerSchemaIsCreated
     */
    public function testThatRequestParametersSchemaIsObtained(Request $request, SwaggerSchema $swaggerSchema): RequestParametersSchema
    {
        return $swaggerSchema->findRequestParametersSchemaByHttpRequest($request);
    }

    /**
     * @depends testThatRequestIsCreated
     * @depends testThatRequestParametersSchemaIsObtained
     */
    public function testThatRequestDataByParametersSchemaIsObtained(Request $request, RequestParametersSchema $requestParametersSchema)
    {
        $requestParameters = new RequestParameters($request);
        $data = $requestParameters->getDataByRequestParametersSchema($requestParametersSchema);

        self::assertObjectHasAttribute('limit', $data);
        self::assertEquals(self::$expectedLimit, $data->limit);

        self::assertObjectHasAttribute('tags', $data);
        self::assertEquals(self::$expectedTags, $data->tags);
    }

    /**
     * @depends testThatRequestIsCreated
     * @depends testThatPetstoreExpandedSwaggerSchemaIsCreated
     * @depends testThatRequestParametersSchemaIsObtained
     * @depends testThatRequestDataByParametersSchemaIsObtained
     */
    public function testThatRequestDataBySwaggerSchemaIsObtained(Request $request, SwaggerSchema $swaggerSchema, RequestParametersSchema $requestParametersSchema)
    {
        $requestParameters = new RequestParameters($request);

        $dataByParametersSchema = $requestParameters->getDataByRequestParametersSchema($requestParametersSchema);
        $dataBySwaggerSchema = $requestParameters->getDataBySwaggerSchema($swaggerSchema);

        self::assertEquals($dataByParametersSchema, $dataBySwaggerSchema);
    }

    /**
     * @depends testThatRequestIsCreated
     * @depends testThatPetstoreSwaggerSchemaIsCreated
     */
    public function testThatRequestDataByParameterlessSwaggerSchemaIsObtained(Request $request, SwaggerSchema $swaggerSchema)
    {
        $requestParameters = new RequestParameters($request);
        $dataBySwaggerSchema = $requestParameters->getDataBySwaggerSchema($swaggerSchema);

        self::assertEmpty((array) $dataBySwaggerSchema);
    }
}
