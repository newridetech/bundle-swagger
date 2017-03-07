<?php

namespace Absolvent\swagger\Exception\SchemaPartNotFound;

use Absolvent\swagger\Breadcrumbs;
use Absolvent\swagger\Exception\SchemaPartNotFound;
use stdClass;

class Method extends SchemaPartNotFound
{
    public $requestPathSchema;

    public function __construct(Breadcrumbs $breadcrumbs, stdClass $requestPathSchema)
    {
        parent::__construct($breadcrumbs);

        $this->requestPathSchema = $requestPathSchema;
    }
}
