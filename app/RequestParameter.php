<?php

namespace Absolvent\swagger;

use stdClass;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use UnexpectedValueException;

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
                return $this->request->request->get($requestParameterSchema->name);
            case 'header':
                return $this->request->headers->get($requestParameterSchema->name);
            case 'query':
                return $this->request->query->get($requestParameterSchema->name);
        }

        throw new UnexpectedValueException($requestParameterSchema->in);
    }
}
