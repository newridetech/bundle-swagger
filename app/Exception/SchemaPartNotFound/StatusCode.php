<?php

namespace Absolvent\swagger\Exception;



class StatusCodeNotFound extends SchemaPartNotFound
{
    public $responsesSchema;

    public function __construct(stdClass $schema, stdClass $responsesSchema)
    {
        parent::__construct($schema);

        $this->responsesSchema = $responsesSchema;
    }
}
