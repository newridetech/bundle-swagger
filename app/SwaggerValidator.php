<?php

namespace Absolvent\swagger;

use Illuminate\Http\Request as IlluminateRequest;
use JsonSchema\Validator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SwaggerValidator
{
    public $jsonSchemaValidatorBuilder;
    public $schema;

    public static function fromFilename($filename)
    {
        return new static(SwaggerSchema::fromFilename($filename));
    }

    public function __construct(SwaggerSchema $schema)
    {
        $this->schema = $schema;
        $this->jsonSchemaValidatorBuilder = new JsonSchemaValidatorBuilder($schema);
    }

    public function validateResponse(Request $request, Response $response): Validator
    {
        $request = IlluminateRequest::createFromBase($request);
        $schema = $this
            ->schema
            ->schema
            ->paths
            ->{$request->getPathInfo()}
            ->{strtolower($request->getMethod())}
            ->responses
            ->{$response->getStatusCode()}
            ->schema
        ;

        $validator = $this
            ->jsonSchemaValidatorBuilder
            ->createJsonSchemaValidator()
        ;

        $data = json_decode($response->getContent());
        $validator->validate($data, $schema);

        return $validator;
    }
}
