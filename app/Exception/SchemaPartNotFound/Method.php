<?php

namespace Absolvent\swagger\Exception;



class MethodNotFound extends SchemaPartNotFound
{
    public $pathSchema;

    public function __construct(stdClass $schema, stdClass $pathSchema)
    {
        parent::__construct($schema);

        $this->pathSchema = $pathSchema;
    }
}
