<?php

namespace Absolvent\swagger;

use Absolvent\swagger\Breadcrumbs\RequestPath\RequestMethod as RequestMethodBreadcrumbs;

class SwaggerRequestMethods
{
    public $swaggerSchema;

    public function __construct(SwaggerSchema $swaggerSchema)
    {
        $this->swaggerSchema = $swaggerSchema;
    }

    public function getRequestMethodBreadcrumbs(): array
    {
        $ret = [];

        $paths = $this->swaggerSchema->get('paths');
        foreach ($paths as $pathname => $pathMethods) {
            foreach ($pathMethods as $pathMethod => $pathResponses) {
                $ret[] = new RequestMethodBreadcrumbs([
                    $pathname,
                    $pathMethod,
                ]);
            }
        }

        return $ret;
    }
}
