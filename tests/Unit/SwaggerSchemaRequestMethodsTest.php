<?php

namespace Newride\swagger\tests\Unit;

use Newride\swagger\Breadcrumbs\RequestPath\RequestMethod as RequestMethodBreadcrumbs;
use Newride\swagger\SwaggerSchema;
use Newride\swagger\SwaggerSchemaRequestMethods;
use PHPUnit\Framework\TestCase;

class SwaggerSchemaRequestMethodsTest extends TestCase
{
    public function testThatSwaggerEmptySchemaIsCreated(): SwaggerSchema
    {
        return SwaggerSchema::fromFilename(__DIR__.'/../../fixtures/empty.yml');
    }

    /**
     * @depends testThatSwaggerEmptySchemaIsCreated
     * @expectedException \Newride\swagger\Exception\SchemaPartNotFound
     */
    public function testThatEmptySchemaIsHandled(SwaggerSchema $swaggerSchema)
    {
        $requestMethods = new SwaggerSchemaRequestMethods($swaggerSchema);

        $requestMethods->getRequestMethodBreadcrumbsList();
    }

    public function testThatSwaggerEmptyPathsSchemaIsCreated(): SwaggerSchema
    {
        return SwaggerSchema::fromFilename(__DIR__.'/../../fixtures/empty-paths.yml');
    }

    /**
     * @depends testThatSwaggerEmptyPathsSchemaIsCreated
     * @expectedException \Newride\swagger\Exception\SchemaPartIsEmpty
     */
    public function testThatEmptyPathsSchemaIsHandled(SwaggerSchema $swaggerSchema)
    {
        $requestMethods = new SwaggerSchemaRequestMethods($swaggerSchema);
        $breadcrumbsList = $requestMethods->getRequestMethodBreadcrumbsList();

        self::assertEmpty($breadcrumbsList);
    }

    public function testThatSwaggerPetstoreSchemaIsCreated(): SwaggerSchema
    {
        return SwaggerSchema::fromFilename(__DIR__.'/../../fixtures/petstore.yml');
    }

    /**
     * @depends testThatSwaggerPetstoreSchemaIsCreated
     */
    public function testThatResponsePathsListIsFound(SwaggerSchema $swaggerSchema)
    {
        $requestMethods = new SwaggerSchemaRequestMethods($swaggerSchema);
        $breadcrumbsList = $requestMethods->getRequestMethodBreadcrumbsList();

        self::assertContainsOnlyInstancesOf(RequestMethodBreadcrumbs::class, $breadcrumbsList);
        self::assertEquals([
            'paths./pet.get',
            'paths./pet.post',
            'paths./pet/photo.post',
        ], array_map('strval', $breadcrumbsList));
    }
}
