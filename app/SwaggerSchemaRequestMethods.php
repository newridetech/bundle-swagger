<?php

namespace Absolvent\swagger;

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
        $ret = [];
        $paths = $this->swaggerSchema->get('paths');

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
