<?php

namespace Absolvent\swagger\Exception\SchemaPartNotFound;

use Absolvent\swagger\Exception\SchemaPartNotFound;
use Symfony\Component\HttpFoundation\Request;

class Path extends SchemaPartNotFound
{
    public static function fromRequest(Request $request): Path
    {
        return new self([
            'paths',
            $request->getPathInfo(),
        ]);
    }
}
