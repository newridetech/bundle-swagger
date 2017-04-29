<?php

namespace Absolvent\swagger\tests\Unit\Exception;

use Absolvent\swagger\Breadcrumbs;
use Absolvent\swagger\Exception\SchemaPartNotFound;
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
