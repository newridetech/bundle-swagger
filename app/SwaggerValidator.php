<?php

namespace Absolvent\swagger;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SwaggerValidator
{
    public $jsonSchemaValidatorBuilder;
    public $schema;

    public static function fromFilename($filename): SwaggerValidator
    {
        return new static(SwaggerSchema::fromFilename($filename));
    }

    public function __construct(SwaggerSchema $schema)
    {
        $this->schema = $schema;
        $this->jsonSchemaValidatorBuilder = new JsonSchemaValidatorBuilder($schema);
    }

    public function validateResponse(Request $request, Response $response): SwaggerValidationResult
    {
        $data = json_decode($response->getContent());
        $schema = $this->schema->findResponseSchemaByHttpResponse($request, $response);

        $validator = $this
            ->jsonSchemaValidatorBuilder
            ->createJsonSchemaValidator()
        ;
        $validator->validate($data, $schema);

        return new SwaggerValidationResult($validator);
    }
}
