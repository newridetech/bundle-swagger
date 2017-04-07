<?php

namespace Absolvent\swagger\Exception\SchemaPartNotFound;

use Absolvent\swagger\Breadcrumbs;
use Absolvent\swagger\Exception\SchemaPartNotFound;
use Absolvent\swagger\JsonSchema;

class Method extends SchemaPartNotFound
{
    public $requestPathSchema;

    public function __construct(Breadcrumbs $breadcrumbs, string $filename, JsonSchema $requestPathSchema)
    {
        parent::__construct($breadcrumbs, $filename);

        $this->requestPathSchema = $requestPathSchema;
    }
}
