<?php

namespace Absolvent\swagger;

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

    public function findBag(stdClass $requestParameterSchema)
    {
        switch ($requestParameterSchema->in) {
            // case 'formData':
            // case 'path':

            case 'body':
                return $this->request->request;
            case 'header':
                return $this->request->headers;
            case 'query':
                return $this->request->query;
        }

        throw new SwaggerUnexpectedFieldValueInException($requestParameterSchema->in);
    }

    public function getData(stdClass $requestParameterSchema, $default = null)
    {
        return $this->findBag($requestParameterSchema)
            ->get($requestParameterSchema->name, $default)
        ;
    }

    public function hasValue(stdClass $requestParameterSchema): bool
    {
        return $this->findBag($requestParameterSchema)
            ->has($requestParameterSchema->name)
        ;
    }
}
