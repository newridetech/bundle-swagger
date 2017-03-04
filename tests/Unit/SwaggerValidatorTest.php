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
        // yield [
        //     'request' => Request::create('http://example.com/pet', 'GET'),
        //     'response' => Response::create(json_encode([
        //         [
        //             'pet_id' => 1,
        //             'pet_name' => 'test',
        //         ],
        //     ]), 200),
        //     'shouldBeValid' => true,
        // ];

        yield [
            'request' => Request::create('http://example.com/pet', 'GET'),
            'response' => Response::create(json_encode([
                [
                    'pet_id' => 1,
                    'pet_name' => 'test',
                ],
                [
                    'pet_id' => 2,
                    'pet_namez' => 'bar',
                ],
            ]), 200),
            'shouldBeValid' => false,
        ];
    }

    public function testThatJsonSchemaValidatorIsCreated(): SwaggerValidator
    {
        return SwaggerValidator::fromFilename(base_path('fixtures/petstore.yml'));
    }

    /**
     * @dataProvider provideRequestResponse
     * @depends testThatJsonSchemaValidatorIsCreated
     */
    public function testThatDataIsValidated(Request $request, Response $response, bool $shouldBeValid, SwaggerValidator $swaggerValidator)
    {
        $validator = $swaggerValidator->validateResponse($request, $response);
        $this->assertSame(
            $shouldBeValid,
            $validator->isValid(),
            json_encode($validator->getErrors(), JSON_PRETTY_PRINT)
        );
    }
}
