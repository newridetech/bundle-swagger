<?php

namespace Newride\swagger\SwaggerValidator;

use Newride\swagger\JsonSchema\RequestParameters as RequestParametersSchema;
use Newride\swagger\JsonSchemaValidatorBuilder;
use Newride\swagger\RequestParameter;
use Newride\swagger\SwaggerSchema;
use Newride\swagger\SwaggerValidationResult;
use Newride\swagger\SwaggerValidator;
use stdClass;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

class HttpRequest extends SwaggerValidator
{
    public $request;
    /** @var SwaggerSchema */
    private $schema;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function validateAgainst(SwaggerSchema $schema): SwaggerValidationResult
    {
        $this->schema = $schema;

        if ($schema->hasRequestParametersSchemaByHttpRequest($this->request)) {
            $requestParametersSchema = $schema->findRequestParametersSchemaByHttpRequest($this->request);
        } else {
            $requestParametersSchema = new RequestParametersSchema([]);
        }

        $validator = (new JsonSchemaValidatorBuilder($schema))->createJsonSchemaValidator();
        $schemaValidationResultList = array_map(function (stdClass $requestParameterSchema) use ($validator) {
            return $this->validateRequestParameterSchema($validator, $requestParameterSchema);
        }, $requestParametersSchema->export());

        return SwaggerValidationResult::fromJsonSchemaValidatorList($schemaValidationResultList);
    }

    /**
     * @param \JsonSchema\Validator $validator
     * @param stdClass $requestParameterSchema
     * @return mixed
     */
    private function validateRequestParameterSchema($validator, stdClass $requestParameterSchema)
    {
        $data = (new RequestParameter($this->request))->getData($requestParameterSchema);

        $jsonSchema = $this->getJsonSchema($requestParameterSchema);

        $required = $this->getIsRequired($requestParameterSchema);

        if ($required || $data !== null) {
            if ($this->isParamFile($requestParameterSchema)) {
                if (!$data instanceof UploadedFile) {
                    $validator->addErrors([
                        [
                            'property' => $requestParameterSchema->name,
                            'pointer' => $requestParameterSchema->name,
                            'message' => "Parameter `{$requestParameterSchema->name}` is invalid file",
                            'constraint' => 'file',
                            'context' => 0,
                        ],
                    ]);
                }
            } else {
                if ($this->isParamFormDataObject($requestParameterSchema, $data)) {
                    $jsonDecoded = json_decode($data, true);
                    if ($jsonDecoded !== null) {
                        $this->request->request->set($requestParameterSchema->name, $jsonDecoded);
                        $data = json_decode($data);
                    }
                } else {
                    $data = json_decode(json_encode($data));
                }
                $validator->validate($data, $jsonSchema);
            }
        }

        return $validator;
    }

    private function isParamFormDataObject($requestParameterSchema, $data): bool
    {
        $jsonSchema = $this->getJsonSchema($requestParameterSchema);
        if ($jsonSchema->{'$ref'} ?? null) {
            $jsonSchema = $this->schema->getByRef($jsonSchema->{'$ref'});
        }
        return $requestParameterSchema->in === 'formData' && ($jsonSchema->type ?? null) === 'object' && is_string($data);
    }

    private function isParamFile($requestParameterSchema): bool
    {
        return ($requestParameterSchema->type ?? null) === 'file';
    }

    private function getJsonSchema($requestParameterSchema)
    {
        return isset($requestParameterSchema->schema) ? $requestParameterSchema->schema : $requestParameterSchema;
    }

    private function getIsRequired($requestParameterSchema): bool
    {
        return isset($requestParameterSchema->required) ? $requestParameterSchema->required : true;
    }
}
