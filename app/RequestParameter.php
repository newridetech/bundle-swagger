<?php

namespace Absolvent\swagger;

use Absolvent\swagger\Exception\SwaggerMissingRequestParameter;
use Absolvent\swagger\Exception\SwaggerUnexpectedFieldValue\In as SwaggerUnexpectedFieldValueInException;
use stdClass;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

class RequestParameter
{
    public $request;

    public function __construct(SymfonyRequest $request)
    {
        $this->request = $request;
    }

    public function getValue(stdClass $requestParameterSchema)
    {
        switch ($requestParameterSchema->in) {
            // case 'formData':
            // case 'path':

            case 'body':
                return $this->getFromBag($this->request->request, $requestParameterSchema->name);
            case 'header':
                return $this->getFromBag($this->request->headers, $requestParameterSchema->name);
            case 'query':
                return $this->getFromBag($this->request->query, $requestParameterSchema->name);
        }

        throw new SwaggerUnexpectedFieldValueInException($requestParameterSchema->in);
    }

    private function getFromBag($headerOrParameterBag, string $parameterName)
    {
        if (!$headerOrParameterBag->has($parameterName)) {
            throw new SwaggerMissingRequestParameter($parameterName);
        }

        return $headerOrParameterBag->get($parameterName);
    }
}
