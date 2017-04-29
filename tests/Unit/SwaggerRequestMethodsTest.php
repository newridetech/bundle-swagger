<?php

namespace Absolvent\swagger\tests\Unit;

use Absolvent\swagger\Breadcrumbs\RequestPath\RequestMethod as RequestMethodBreadcrumbs;
use Absolvent\swagger\SwaggerRequestMethods;
use Absolvent\swagger\SwaggerSchema;
use PHPUnit\Framework\TestCase;

class SwaggerRequestMethodsTest extends TestCase
{
    public function testThatSwaggerSchemaIsCreated(): SwaggerSchema
    {
        return SwaggerSchema::fromFilename(__DIR__.'/../../fixtures/petstore.yml');
    }

    /**
     * @depends testThatSwaggerSchemaIsCreated
     */
    public function testTharSwaggerRequestMethodsAreFound(SwaggerSchema $swaggerSchema)
    {
        $swaggerRequestMethods = new SwaggerRequestMethods($swaggerSchema);
        $requestMethodBreadcrumbs = $swaggerRequestMethods->getRequestMethodBreadcrumbs();

        self::assertNotEmpty($requestMethodBreadcrumbs);
        self::assertContainsOnlyInstancesOf(RequestMethodBreadcrumbs::class, $requestMethodBreadcrumbs);
    }
}
