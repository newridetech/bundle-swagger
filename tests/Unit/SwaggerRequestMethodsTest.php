<?php

namespace Absolvent\swagger\tests\Unit;

use Absolvent\swagger\Breadcrumbs\RequestPath\RequestMethod as RequestMethodBreadcrumbs;
use Absolvent\swagger\SwaggerRequestMethods;
use Absolvent\swagger\SwaggerSchema;
use Absolvent\swagger\tests\TestCase;

class SwaggerRequestMethodsTest extends TestCase
{
    public function testThatSwaggerSchemaIsCreated(): SwaggerSchema
    {
        return SwaggerSchema::fromFilename(base_path('fixtures/petstore.yml'));
    }

    /**
     * @depends testThatSwaggerSchemaIsCreated
     */
    public function testTharSwaggerRequestMethodsAreFound(SwaggerSchema $swaggerSchema)
    {
        $swaggerRequestMethods = new SwaggerRequestMethods($swaggerSchema);
        $requestMethodBreadcrumbs = $swaggerRequestMethods->getRequestMethodBreadcrumbs();

        $this->assertNotEmpty($requestMethodBreadcrumbs);
        $this->assertContainsOnlyInstancesOf(RequestMethodBreadcrumbs::class, $requestMethodBreadcrumbs);
    }
}
