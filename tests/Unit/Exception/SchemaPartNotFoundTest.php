<?php

namespace Newride\swagger\tests\Unit\Exception;

use Newride\swagger\Breadcrumbs;
use Newride\swagger\Exception\SchemaPartNotFound;
use PHPUnit\Framework\TestCase;

class SchemaPartNotFoundTest extends TestCase
{
    public function testThatEntireSchemaPathIsDumpedAndDebuggable()
    {
        $breadcrumbs = new Breadcrumbs([
            'foo',
            'bar',
        ]);
        $e = new SchemaPartNotFound($breadcrumbs, 'test.yml');
        self::assertContains(strval($breadcrumbs), $e->getMessage());
    }
}
