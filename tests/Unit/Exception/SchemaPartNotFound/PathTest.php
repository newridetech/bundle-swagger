<?php

namespace Absolvent\swagger\tests\Unit\Exception\SchemaPartNotFound;

use Absolvent\swagger\Exception\SchemaPartNotFound\Path;
use Absolvent\swagger\tests\TestCase;
use Symfony\Component\HttpFoundation\Request;

class PathTest extends TestCase
{
    public function testThatEntireSchemaPathIsDumpedAndDebuggable()
    {
        $request = Request::create('http://example.com/pet', 'GET');
        $e = Path::fromRequest($request);
        $this->assertEquals('paths./pet', $e->getBreadcrumbsPath());
    }
}
