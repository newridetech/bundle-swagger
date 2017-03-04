<?php

namespace Absolvent\swagger\Exception\SchemaPartNotFound;

use Absolvent\swagger\Exception\SchemaPartNotFound;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class StatusCode extends SchemaPartNotFound
{
    public static function fromResponse(Request $request, Response $response): StatusCode
    {
        return new self([
            'paths',
            $request->getPathInfo(),
            strtolower($request->getMethod()),
            $response->getStatusCode(),
        ]);
    }
}
