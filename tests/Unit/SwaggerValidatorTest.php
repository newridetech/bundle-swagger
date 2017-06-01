<?php

namespace Absolvent\swagger\tests\Unit;

use Absolvent\swagger\SwaggerSchema;
use Absolvent\swagger\SwaggerValidator\HttpRequest as HttpRequestValidator;
use Absolvent\swagger\SwaggerValidator\HttpResponse as HttpResponseValidator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SwaggerValidatorTest extends TestCase
{
    public function provideRequestResponse()
    {
        yield 'valid request-response' => [
            'fixtureFilename' => __DIR__.'/../../fixtures/petstore.yml',
            'request' => Request::create('http://example.com/api/pet', 'GET', ['filter' => '123']),
            'isRequestValid' => true,
            'response' => Response::create(json_encode([
                [
                    'pet_id' => 1,
                    'pet_name' => 'test',
                ],
            ]), 200),
            'isResponseValid' => true,
        ];

        yield 'invalid request parameter type' => [
            'fixtureFilename' => __DIR__.'/../../fixtures/petstore.yml',
            'request' => Request::create('http://example.com/api/pet', 'GET', ['filter' => 123]),
            'isRequestValid' => false,
            'response' => Response::create(json_encode([
                'code' => 1,
                'message' => 'some error',
                'fields' => ['string fields'],
            ]), 500),
            'isResponseValid' => true,
        ];

        yield 'extra field' => [
            'fixtureFilename' => __DIR__.'/../../fixtures/petstore.yml',
            'request' => Request::create('http://example.com/api/pet', 'GET'),
            'isRequestValid' => true,
            'response' => Response::create(json_encode([
                [
                    'pet_id' => 2,
                    'pet_namez' => 'bar',
                ],
            ]), 200),
            'isResponseValid' => false,
        ];

        yield 'type coercion' => [
            'fixtureFilename' => __DIR__.'/../../fixtures/petstore.yml',
            'request' => Request::create('http://example.com/api/pet', 'GET'),
            'isRequestValid' => true,
            'response' => Response::create(json_encode([
                [
                    'pet_id' => '1',
                    'pet_name' => 'test',
                ],
                [
                    'pet_id' => '2',
                    'pet_name' => 'bar',
                ],
            ]), 200),
            'isResponseValid' => true,
        ];

        yield 'at least one item' => [
            'fixtureFilename' => __DIR__.'/../../fixtures/petstore.yml',
            'request' => Request::create('http://example.com/api/pet', 'GET'),
            'isRequestValid' => true,
            'response' => Response::create(json_encode([]), 200),
            'isResponseValid' => false,
        ];

        yield 'body post json' => [
            'fixtureFilename' => __DIR__.'/../../fixtures/petstore.yml',
            'request' => Request::create('http://example.com/api/pet', 'POST', [], [], [], [], json_encode([
                'body' => [
                    'pet_name' => 'foo',
                ],
            ])),
            'isRequestValid' => true,
            'response' => Response::create(json_encode([
                'pet_id' => 123,
                'pet_name' => 'foo',
            ]), 200),
            'isResponseValid' => true,
        ];

        yield 'valid input parameters' => [
            'fixtureFilename' => __DIR__.'/../../fixtures/petstore-expanded.yml',
            'request' => Request::create('http://petstore.swagger.io/api/pets', 'GET', [
                'tags' => ['a', 'b'],
                'limit' => 0,
            ]),
            'isRequestValid' => true,
            'response' => Response::create(json_encode([]), 200),
            'isResponseValid' => true,
        ];

        yield 'invalid input parameters' => [
            'fixtureFilename' => __DIR__.'/../../fixtures/petstore-expanded.yml',
            'request' => Request::create('http://petstore.swagger.io/api/pets', 'GET', [
                'limit' => 'foo',
            ]),
            'isRequestValid' => false,
            'response' => Response::create(json_encode([
                'code' => 1,
                'message' => 'some error',
                'fields' => 'string fields',
            ]), 500),
            'isResponseValid' => true,
        ];

        yield 'body parameters' => [
            'fixtureFilename' => __DIR__.'/../../fixtures/petstore-expanded.yml',
            'request' => Request::create('http://petstore.swagger.io/api/pets', 'POST', [], [], [], [], json_encode([
                'pet' => [
                    'name' => 'foo',
                    'tag' => 'bar',
                ],
            ])),
            'isRequestValid' => true,
            'response' => Response::create(json_encode([]), 200),
            'isResponseValid' => true,
        ];

        yield 'invalid body parameters' => [
            'fixtureFilename' => __DIR__.'/../../fixtures/petstore-expanded.yml',
            'request' => Request::create('http://petstore.swagger.io/api/pets', 'POST', [], [], [], [], json_encode([
                'pet' => [
                    'name' => 5,
                ],
            ])),
            'isRequestValid' => false,
            'response' => Response::create(json_encode([]), 200),
            'isResponseValid' => true,
        ];
    }

    /**
     * @dataProvider provideRequestResponse
     */
    public function testThatDataIsValidated(string $fixtureFilename, Request $request, bool $isRequestValid, Response $response, bool $isResponseValid)
    {
        $swaggerSchema = SwaggerSchema::fromFilename($fixtureFilename);

        $requestValidator = (new HttpRequestValidator($request))->validateAgainst($swaggerSchema);
        self::assertSame(
            $isRequestValid,
            $requestValidator->isValid(),
            'Request is invalid: '.$requestValidator->getException()->getMessage()
        );

        $responseValidator = (new HttpResponseValidator($request, $response))->validateAgainst($swaggerSchema);
        self::assertSame(
            $isResponseValid,
            $responseValidator->isValid(),
            'Response is invalid: '.$responseValidator->getException()->getMessage()
        );
    }
}
