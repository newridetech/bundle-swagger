<?php

namespace Absolvent\swagger\tests\Unit;

use Absolvent\swagger\Breadcrumbs\RequestPath\RequestMethod as RequestMethodBreadcrumbs;
use Absolvent\swagger\SwaggerSchema;
use Absolvent\swagger\SwaggerSchemaRequestMethods;
use Absolvent\swagger\tests\TestCase;

class SwaggerSchemaRequestMethodsTest extends TestCase
{
    public function testThatSwaggerSchemaIsCreated(): SwaggerSchema
    {
        return SwaggerSchema::fromFilename(base_path('fixtures/petstore.yml'));
    }

    /**
     * @depends testThatSwaggerSchemaIsCreated
     */
    public function testThatResponsePathsListIsFound(SwaggerSchema $swaggerSchema)
    {
        $requestMethods = new SwaggerSchemaRequestMethods($swaggerSchema);
        $breadcrumbsList = $requestMethods->getRequestMethodBreadcrumbsList();

        $this->assertContainsOnlyInstancesOf(RequestMethodBreadcrumbs::class, $breadcrumbsList);
        $this->assertEquals([
            'paths./pet.get',
        ], array_map('strval', $breadcrumbsList));
    }
}
