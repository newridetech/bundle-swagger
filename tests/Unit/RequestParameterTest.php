<?php

namespace Absolvent\swagger\tests\Unit;

use Absolvent\swagger\RequestParameter;
use PHPUnit\Framework\TestCase;
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
            'expectedValue' => 'bar',
        ];

        // yield 'plain post request' => [
        //     'request' => Request::create('http://example.com/api/pets', 'POST', [], [], [], [], json_encode([
        //         'foo' => 'bar',
        //     ])),
        //     'requestParameterSchema' => (object) [
        //         'in' => 'body',
        //         'name' => 'foo',
        //     ],
        //     'expectedValue' => 'bar',
        // ];

        yield 'form data request' => [
            'request' => Request::create(
                'http://example.com/api/pets',
                'POST',
                $parameters = [],
                $cookies = [],
                $files = [],
                $server = [],
                json_encode([
                    'foo' => 'bar',
                ])
            ),
            'requestParameterSchema' => (object) [
                'in' => 'formData',
                'name' => 'foo',
            ],
            'expectedValue' => 'bar',
        ];
    }

    /**
     * @dataProvider provideRequestParameters
     */
    public function testThatQueryParameterDataIsObtained(Request $request, stdClass $requestParameterSchema, $expectedValue)
    {
        $requestParameter = new RequestParameter($request);

        self::assertEquals($expectedValue, $requestParameter->getData($requestParameterSchema));
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
        self::assertEquals(
            $default,
            $requestParameter->getData($requestParameterSchema, $default)
        );
    }
}
