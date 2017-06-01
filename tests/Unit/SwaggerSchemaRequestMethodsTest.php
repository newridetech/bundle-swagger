<?php

namespace Absolvent\swagger\tests\Unit;

use Absolvent\swagger\Breadcrumbs\RequestPath\RequestMethod as RequestMethodBreadcrumbs;
use Absolvent\swagger\SwaggerSchema;
use Absolvent\swagger\SwaggerSchemaRequestMethods;
use PHPUnit\Framework\TestCase;

class SwaggerSchemaRequestMethodsTest extends TestCase
{
    public function testThatSwaggerEmptySchemaIsCreated(): SwaggerSchema
    {
        return SwaggerSchema::fromFilename(__DIR__.'/../../fixtures/empty.yml');
    }

    /**
     * @depends testThatSwaggerEmptySchemaIsCreated
     * @expectedException \Absolvent\swagger\Exception\SchemaPartNotFound
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
     * @expectedException \Absolvent\swagger\Exception\SchemaPartIsEmpty
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
        ], array_map('strval', $breadcrumbsList));
    }
}
