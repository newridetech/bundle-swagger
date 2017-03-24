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
            'response' => Response::create(json_encode([
                [
                    'pet_id' => 1,
                    'pet_name' => 'test',
                ],
            ]), 200),
            'shouldBeValid' => true,
        ];

        yield 'extra field' => [
            'fixtureFilename' => 'fixtures/petstore.yml',
            'request' => Request::create('http://example.com/api/pet', 'GET'),
            'response' => Response::create(json_encode([
                [
                    'pet_id' => 2,
                    'pet_namez' => 'bar',
                ],
            ]), 200),
            'shouldBeValid' => false,
        ];

        yield 'type coercion' => [
            'fixtureFilename' => 'fixtures/petstore.yml',
            'request' => Request::create('http://example.com/api/pet', 'GET'),
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
            'shouldBeValid' => true,
        ];

        yield 'at least one item' => [
            'fixtureFilename' => 'fixtures/petstore.yml',
            'request' => Request::create('http://example.com/api/pet', 'GET'),
            'response' => Response::create(json_encode([]), 200),
            'shouldBeValid' => false,
        ];
    }

    /**
     * @dataProvider provideRequestResponse
     */
    public function testThatDataIsValidated(string $fixtureFilename, Request $request, Response $response, bool $shouldBeValid)
    {
        $swaggerValidator = SwaggerValidator::fromFilename(base_path($fixtureFilename));
        $validator = $swaggerValidator->validateResponse($request, $response);
        $this->assertSame(
            $shouldBeValid,
            $validator->isValid(),
            $validator->getException()->getMessage()
        );
    }
}
