<?php

namespace Absolvent\swagger\tests\Unit\Exception;

use Absolvent\swagger\Breadcrumbs;
use Absolvent\swagger\Exception\SchemaPartNotFound;
use Absolvent\swagger\tests\TestCase;

class SchemaPartNotFoundTest extends TestCase
{
    public function testThatEntireSchemaPathIsDumpedAndDebuggable()
    {
        $breadcrumbs = new Breadcrumbs([
            'foo',
            'bar',
        ]);
        $e = new SchemaPartNotFound($breadcrumbs);
        $this->assertContains(strval($breadcrumbs), $e->getMessage());
    }
}
