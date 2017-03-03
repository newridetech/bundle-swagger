<?php

namespace Absolvent\swagger\Exception;

use stdClass;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class SchemaPartNotFound extends BadRequestHttpException
{
    public $schema;

    public function __construct(stdClass $schema)
    {
        $this->schema = $schema;
    }
}
