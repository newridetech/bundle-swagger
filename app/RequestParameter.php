<?php

namespace Absolvent\swagger;

use Absolvent\swagger\Exception\SwaggerUnexpectedFieldValue\In as SwaggerUnexpectedFieldValueInException;
use stdClass;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

class RequestParameter
{
    public $request;

    public static function isFormData(stdClass $requestParameterSchema): bool
    {
        return 'formData' === $requestParameterSchema->in;
    }

    public function __construct(SymfonyRequest $request)
    {
        $this->request = $request;
    }

    public function findBag(stdClass $requestParameterSchema)
    {
        switch ($requestParameterSchema->in) {
            // case 'path':

            case 'body':
                $payload = json_decode($this->request->getContent(), $asArray = true);

                return new ParameterBag($payload);
            case 'formData':
                return $this->getRequestFormDataParameterBag();
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

    public function getRequestFormDataParameterBag(): ParameterBag
    {
        $parameters = $this->request->request->all();
        return new ParameterBag($parameters);
    }

    public function hasValue(stdClass $requestParameterSchema): bool
    {
        return $this->findBag($requestParameterSchema)
            ->has($requestParameterSchema->name)
        ;
    }
}
