<?php

namespace Absolvent\swagger;

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

    public function getDataByRequestParametersSchema(array $requestParametersSchema): stdClass
    {
        $ret = new stdClass();
        foreach ($requestParametersSchema as $requestParameterSchema) {
            $requestParameter = new RequestParameter($this->request);
            $ret->{$requestParameterSchema->name} = $requestParameter->getValue($requestParameterSchema);
        }

        return $ret;
    }
}
