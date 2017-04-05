<?php

namespace Absolvent\swagger\SwaggerValidator;

use Absolvent\swagger\JsonSchemaValidatorBuilder;
use Absolvent\swagger\SwaggerSchema;
use Absolvent\swagger\SwaggerValidationResult;
use Absolvent\swagger\SwaggerValidator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class HttpResponse extends SwaggerValidator
{
    public $request;
    public $response;

    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    public function validateAgainst(SwaggerSchema $schema): SwaggerValidationResult
    {
        $responseSchema = $schema->findResponseSchemaByHttpResponse($this->request, $this->response);
        $data = json_decode($this->response->getContent());

        $validator = (new JsonSchemaValidatorBuilder($schema))->createJsonSchemaValidator();
        $validator->validate($data, $responseSchema->export());

        return SwaggerValidationResult::fromJsonSchemaValidator($validator);
    }
}
