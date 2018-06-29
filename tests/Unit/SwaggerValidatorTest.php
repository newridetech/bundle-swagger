<?php

namespace Newride\swagger\tests\Unit;

use Newride\swagger\SwaggerSchema;
use Newride\swagger\SwaggerValidator\HttpRequest as HttpRequestValidator;
use Newride\swagger\SwaggerValidator\HttpResponse as HttpResponseValidator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
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

        yield 'post file' => [
            'fixtureFilename' => __DIR__.'/../../fixtures/petstore.yml',
            'request' => Request::create('http://petstore.swagger.io/api/pet/photo', 'POST', [
                'pet_id' => 1,
            ], [], [
                'photo' => new UploadedFile(__DIR__.'/../../fixtures/petstore.yml', 'views/welcome.blade.php'),
            ]),
            'isRequestValid' => true,
            'response' => Response::create(json_encode([
                'pet_id' => 1,
                'photo_url' => 'http://petstore.swagger.io/pets/img.png',
            ]), 200),
            'isResponseValid' => true,
        ];

        yield 'post file missing' => [
            'fixtureFilename' => __DIR__.'/../../fixtures/petstore.yml',
            'request' => Request::create('http://petstore.swagger.io/api/pet/photo', 'POST', [
                'pet_id' => 1,
            ], []),
            'isRequestValid' => false,
            'response' => Response::create(json_encode([
                'pet_id' => 1,
                'photo_url' => 'http://petstore.swagger.io/pets/img.png',
            ]), 200),
            'isResponseValid' => true,
        ];

        yield 'post json along with file' => [
            'fixtureFilename' => __DIR__.'/../../fixtures/petstore-expanded.yml',
            'request' => Request::create('http://petstore.swagger.io/api/pet', 'POST', [
                'pet' => json_encode([
                    'name' => 'foo',
                    'tag' => 'bar',
                ]),
            ], [], [
                'image' => new UploadedFile(__DIR__.'/../../fixtures/petstore-expanded.yml', 'views/welcome.blade.php'),
            ]),
            'isRequestValid' => true,
            'response' => Response::create(json_encode([]), 200),
            'isResponseValid' => true,
        ];
    }

    /**
     * @dataProvider provideRequestResponse
     *
     * @param string   $fixtureFilename
     * @param Request  $request
     * @param bool     $isRequestValid
     * @param Response $response
     * @param bool     $isResponseValid
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
