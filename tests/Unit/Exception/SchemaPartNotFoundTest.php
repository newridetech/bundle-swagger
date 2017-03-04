<?php

namespace Absolvent\swagger\tests\Unit\Exception;

use Absolvent\swagger\Exception\SchemaPartNotFound;
use Absolvent\swagger\tests\TestCase;

class SchemaPartNotFoundTest extends TestCase
{
    public function testThatEntireSchemaPathIsDumpedAndDebuggable()
    {
        $e = new SchemaPartNotFound([
            'foo',
            'bar',
        ]);
        $this->assertEquals('foo.bar', $e->getBreadcrumbsPath());
    }
}
