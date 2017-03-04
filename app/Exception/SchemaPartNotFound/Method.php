<?php

namespace Absolvent\swagger\Exception\SchemaPartNotFound;

use Absolvent\swagger\Exception\SchemaPartNotFound;
use Symfony\Component\HttpFoundation\Request;

class Method extends SchemaPartNotFound
{
    public static function fromRequest(Request $request): Method
    {
        return new self([
            'paths',
            $request->getPathInfo(),
            strtolower($request->getMethod()),
        ]);
    }
}
