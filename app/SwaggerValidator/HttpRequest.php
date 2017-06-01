<?php

namespace Absolvent\swagger\SwaggerValidator;

use Absolvent\swagger\JsonSchema\RequestParameters as RequestParametersSchema;
use Absolvent\swagger\JsonSchemaValidatorBuilder;
use Absolvent\swagger\RequestParameter;
use Absolvent\swagger\SwaggerSchema;
use Absolvent\swagger\SwaggerValidationResult;
use Absolvent\swagger\SwaggerValidator;
use stdClass;
use Symfony\Component\HttpFoundation\Request;

class HttpRequest extends SwaggerValidator
{
    public $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function validateAgainst(SwaggerSchema $schema): SwaggerValidationResult
    {
        if ($schema->hasRequestParametersSchemaByHttpRequest($this->request)) {
            $requestParametersSchema = $schema->findRequestParametersSchemaByHttpRequest($this->request);
        } else {
            $requestParametersSchema = new RequestParametersSchema([]);
        }

        $validator = (new JsonSchemaValidatorBuilder($schema))->createJsonSchemaValidator();
        $schemaValidationResultList = array_map(function (stdClass $requestParameterSchema) use ($validator) {
            return static::validateRequestParameterSchema($this->request, $validator, $requestParameterSchema);
        }, $requestParametersSchema->export());

        return SwaggerValidationResult::fromJsonSchemaValidatorList($schemaValidationResultList);
    }

    private static function validateRequestParameterSchema(Request $request, $validator, stdClass $requestParameterSchema)
    {
        $data = (new RequestParameter($request))->getData($requestParameterSchema);
        $data = json_decode(json_encode($data));

        if (isset($requestParameterSchema->schema)) {
            $jsonSchema = $requestParameterSchema->schema;
        } else {
            $jsonSchema = $requestParameterSchema;
        }

        $required = isset($requestParameterSchema->required) ? $requestParameterSchema->required : true;

        if ($required || $data !== null) {
            $validator->validate($data, $jsonSchema);
        }

        return $validator;
    }
}
