<?php

namespace Absolvent\swagger;

use Absolvent\swagger\Exception\SchemaPartIsEmpty;
use Absolvent\swagger\Exception\SchemaPartNotFound;
use Absolvent\swagger\Breadcrumbs\RequestPath\RequestMethod as RequestMethodBreadcrumbs;

class SwaggerSchemaRequestMethods
{
    public $swaggerSchema;

    public function __construct(SwaggerSchema $swaggerSchema)
    {
        $this->swaggerSchema = $swaggerSchema;
    }

    public function getRequestMethodBreadcrumbsList(): array
    {
        $pathsBreadcrumbs = new Breadcrumbs(['paths']);

        $ret = [];
        if (!$this->swaggerSchema->has($pathsBreadcrumbs)) {
            throw new SchemaPartNotFound($pathsBreadcrumbs, $this->swaggerSchema->filename);
        }

        $paths = $this->swaggerSchema->get($pathsBreadcrumbs);
        if (!is_object($paths)) {
            throw new SchemaPartIsEmpty($pathsBreadcrumbs, $this->swaggerSchema->filename);
        }

        foreach ($paths as $pathname => $pathMethods) {
            foreach ($pathMethods as $pathMethod => $pathDescription) {
                $ret[] = new RequestMethodBreadcrumbs([
                    'paths',
                    $pathname,
                    $pathMethod,
                ]);
            }
        }

        return $ret;
    }
}
