<?php

namespace Absolvent\swagger;

use Absolvent\swagger\JsonSchema\RequestParameters as RequestParametersSchema;
use Illuminate\Http\Request;
use stdClass;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

class RequestParameters
{
    public $request;

    public function __construct(SymfonyRequest $request)
    {
        $this->request = Request::createFromBase($request);
    }

    public function getDataBySwaggerSchema(SwaggerSchema $swaggerSchema): stdClass
    {
        // if (!$swaggerSchema->hasRequestParametersSchemaByHttpRequest($this->request)) {
        //     return new stdClass();
        // }

        $requestParametersSchema = $swaggerSchema->findRequestParametersSchemaByHttpRequest($this->request);

        return $this->getDataByRequestParametersSchema($requestParametersSchema);
    }

    public function getDataByRequestParametersSchema(RequestParametersSchema $requestParametersSchema): stdClass
    {
        $ret = new stdClass();
        foreach ($requestParametersSchema as $requestParameterSchema) {
            $requestParameter = new RequestParameter($this->request);
            if ($requestParameter->hasValue($requestParameterSchema)) {
                $ret->{$requestParameterSchema->name} = $requestParameter->getData($requestParameterSchema);
            }
        }

        return $ret;
    }
}
