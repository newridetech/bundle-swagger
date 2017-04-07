<?php

namespace Absolvent\swagger;

use Absolvent\swagger\Exception\SchemaPartNotFound\Paths;
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
            throw new Paths($pathsBreadcrumbs, $this->swaggerSchema->filename);
        }

        $paths = $this->swaggerSchema->get($pathsBreadcrumbs);
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
