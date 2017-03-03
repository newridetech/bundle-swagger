<?php

namespace Absolvent\swagger\Exception;

use stdClass;

class PathNotFound extends SchemaPartNotFound
{
    public $pathsSchema;

    public function __construct(stdClass $schema, stdClass $pathsSchema)
    {
        parent::__construct($schema);

        $this->pathsSchema = $pathsSchema;
    }
}
