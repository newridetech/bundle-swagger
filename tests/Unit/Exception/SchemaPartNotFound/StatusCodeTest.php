<?php

namespace Absolvent\swagger\tests\Unit\Exception\SchemaPartNotFound;

use Absolvent\swagger\Exception\SchemaPartNotFound\StatusCode;
use Absolvent\swagger\tests\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class StatusCodeTest extends TestCase
{
    public function testThatEntireSchemaStatusCodeIsDumpedAndDebuggable()
    {
        $request = Request::create('http://example.com/pet', 'GET');
        $response = Response::create('', 200);
        $e = StatusCode::fromResponse($request, $response);
        $this->assertEquals('paths./pet.get.200', $e->getBreadcrumbsPath());
    }
}
