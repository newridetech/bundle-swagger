<?php

namespace Absolvent\swagger\Exception\SchemaPartNotFound;

use Absolvent\swagger\Breadcrumbs;
use Absolvent\swagger\Exception\SchemaPartNotFound;
use stdClass;

class StatusCode extends SchemaPartNotFound
{
    public $requestMethodSchema;

    public function __construct(Breadcrumbs $breadcrumbs, stdClass $requestMethodSchema)
    {
        parent::__construct($breadcrumbs);

        $this->requestMethodSchema = $requestMethodSchema;
    }
}
