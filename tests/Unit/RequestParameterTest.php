<?php

namespace Absolvent\swagger\tests\Unit;

use Absolvent\swagger\RequestParameter;
use Absolvent\swagger\tests\TestCase;
use stdClass;
use Symfony\Component\HttpFoundation\Request;

class RequestParameterTest extends TestCase
{
    public function provideRequestParameters()
    {
        yield 'plain get request' => [
            'request' => Request::create('http://example.com/api/pets', 'GET', [
                'foo' => 'bar',
            ]),
            'requestParameterSchema' => (object) [
                'in' => 'query',
                'name' => 'foo',
            ],
            'expectedKey' => 'foo',
            'expectedValue' => 'bar',
        ];

        yield 'check headers' => [
            'request' => Request::create('http://example.com/api/pets', 'GET', [], [], [], [
                'HTTP_foo' => 'bar',
            ]),
            'requestParameterSchema' => (object) [
                'in' => 'header',
                'name' => 'foo',
            ],
            'expectedKey' => 'foo',
            'expectedValue' => 'bar',
        ];

        yield 'plain post request' => [
            'request' => Request::create('http://example.com/api/pets', 'POST', [
                'foo' => 'bar',
            ]),
            'requestParameterSchema' => (object) [
                'in' => 'body',
                'name' => 'foo',
            ],
            'expectedKey' => 'foo',
            'expectedValue' => 'bar',
        ];
    }

    /**
     * @dataProvider provideRequestParameters
     */
    public function testThatQueryParameterDataIsObtained(Request $request, stdClass $requestParameterSchema, $expectedKey, $expectedValue)
    {
        $requestParameter = new RequestParameter($request);

        $this->assertEquals($expectedValue, $requestParameter->getData($requestParameterSchema));
    }

    /**
     * @expectedException \Absolvent\swagger\Exception\SwaggerUnexpectedFieldValue\In
     * @expectedExceptionMessage foo
     */
    public function testThatUnknownInFieldThrowsException()
    {
        $request = Request::create('http://example.com/api/pets', 'GET');
        $requestParameter = new RequestParameter($request);
        $requestParameterSchema = (object) [
            'in' => 'foo',
            'name' => 'bar',
        ];

        $requestParameter->getData($requestParameterSchema);
    }

    public function testThatDefaultValueIsPicked()
    {
        $request = Request::create('http://example.com/api/pets', 'GET');
        $requestParameter = new RequestParameter($request);
        $requestParameterSchema = (object) [
            'in' => 'query',
            'name' => 'bar',
        ];

        $default = 'baz';
        $this->assertEquals(
            $default,
            $requestParameter->getData($requestParameterSchema, $default)
        );
    }
}
