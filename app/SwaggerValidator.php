<?php

namespace Absolvent\swagger;

use Absolvent\swagger\JsonSchema\RequestParameters as RequestParametersSchema;
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

    public function validateData($data, JsonSchema $schema): SwaggerValidationResult
    {
        $validator = $this
            ->jsonSchemaValidatorBuilder
            ->createJsonSchemaValidator()
        ;
        $validator->validate($data, $schema);

        return new SwaggerValidationResult($validator);
    }

    public function validateRequest(Request $request): SwaggerValidationResult
    {
        $breadcrumbs = $this->schema->findRequestParametersBreadcrumbsByHttpRequest($request);
        if ($this->schema->has($breadcrumbs)) {
            $schema = $this->schema->findRequestParametersSchemaByHttpRequest($request);
        } else {
            $schema = new RequestParametersSchema([]);
        }
        $data = (new RequestParameters($request))->getDataByRequestParametersSchema($schema);

        return $this->validateData($data, $schema);
    }

    public function validateResponse(Request $request, Response $response): SwaggerValidationResult
    {
        $schema = $this->schema->findResponseSchemaByHttpResponse($request, $response);
        $data = json_decode($response->getContent());

        return $this->validateData($data, $schema);
    }
}
