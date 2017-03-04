<?php

namespace Absolvent\swagger\tests\Unit\Exception\SchemaPartNotFound;

use Absolvent\swagger\Exception\SchemaPartNotFound\Method;
use Absolvent\swagger\tests\TestCase;
use Symfony\Component\HttpFoundation\Request;

class MethodTest extends TestCase
{
    public function testThatEntireSchemaMethodIsDumpedAndDebuggable()
    {
        $request = Request::create('http://example.com/pet', 'GET');
        $e = Method::fromRequest($request);
        $this->assertEquals('paths./pet.get', $e->getBreadcrumbsPath());
    }
}
