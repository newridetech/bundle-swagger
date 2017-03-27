<?php

namespace Absolvent\swagger\tests\Unit;

use Absolvent\swagger\SwaggerValidator;
use Absolvent\swagger\tests\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SwaggerValidatorTest extends TestCase
{
    public function provideRequestResponse()
    {
        yield 'valid request-response' => [
            'fixtureFilename' => 'fixtures/petstore.yml',
            'request' => Request::create('http://example.com/api/pet', 'GET'),
            'isRequestValid' => true,
            'response' => Response::create(json_encode([
                [
                    'pet_id' => 1,
                    'pet_name' => 'test',
                ],
            ]), 200),
            'isResponseValid' => true,
        ];

        yield 'extra field' => [
            'fixtureFilename' => 'fixtures/petstore.yml',
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
            'fixtureFilename' => 'fixtures/petstore.yml',
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
            'fixtureFilename' => 'fixtures/petstore.yml',
            'request' => Request::create('http://example.com/api/pet', 'GET'),
            'isRequestValid' => true,
            'response' => Response::create(json_encode([]), 200),
            'isResponseValid' => false,
        ];

        yield 'valid input parameters' => [
            'fixtureFilename' => 'fixtures/petstore-expanded.yml',
            'request' => Request::create('http://petstore.swagger.io/api/pets', 'GET', [
                'tags' => 0,
                'limit' => 0,
            ]),
            'isRequestValid' => true,
            'response' => Response::create(json_encode([]), 200),
            'isResponseValid' => true,
        ];

        yield 'invalid input parameters' => [
            'fixtureFilename' => 'fixtures/petstore-expanded.yml',
            'request' => Request::create('http://petstore.swagger.io/api/pets', 'GET', [
                'latitude' => 'foo',
                'longitude' => 'bar',
            ]),
            'isRequestValid' => false,
            'response' => Response::create(json_encode([
                'code' => 1,
                'message' => 'some error',
                'fields' => 'string fields',
            ]), 500),
            'isResponseValid' => true,
        ];
    }

    /**
     * @dataProvider provideRequestResponse
     */
    public function testThatDataIsValidated(string $fixtureFilename, Request $request, bool $isRequestValid, Response $response, bool $isResponseValid)
    {
        $swaggerValidator = SwaggerValidator::fromFilename(base_path($fixtureFilename));

        $requestValidator = $swaggerValidator->validateRequest($request);
        $this->assertSame(
            $isRequestValid,
            $requestValidator->isValid(),
            $requestValidator->getException()->getMessage()
        );

        $responseValidator = $swaggerValidator->validateResponse($request, $response);
        $this->assertSame(
            $isResponseValid,
            $responseValidator->isValid(),
            $responseValidator->getException()->getMessage()
        );
    }
}
