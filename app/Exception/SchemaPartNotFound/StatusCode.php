<?php

namespace Absolvent\swagger\Exception\SchemaPartNotFound;

use Absolvent\swagger\Breadcrumbs;
use Absolvent\swagger\Exception\SchemaPartNotFound;
use Absolvent\swagger\JsonSchema;

class StatusCode extends SchemaPartNotFound
{
    public $requestMethodSchema;

    public function __construct(Breadcrumbs $breadcrumbs, string $filename, JsonSchema $requestMethodSchema)
    {
        parent::__construct($breadcrumbs, $filename);

        $this->requestMethodSchema = $requestMethodSchema;
    }
}
