<?php

namespace Absolvent\swagger;

use Absolvent\swagger\Breadcrumbs\RequestPath as RequestPathBreadcrumbs;
use Absolvent\swagger\Breadcrumbs\RequestPath\RequestMethod as RequestMethodBreadcrumbs;
use Absolvent\swagger\Breadcrumbs\RequestPath\RequestMethod\ResponsePath as ResponsePathBreadcrumbs;
use Absolvent\swagger\Exception\SchemaPartNotFound\Method;
use Absolvent\swagger\Exception\SchemaPartNotFound\Path;
use Absolvent\swagger\Exception\SchemaPartNotFound\StatusCode;
use Dflydev\DotAccessData\Data;
use stdClass;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Yaml\Yaml;

class SwaggerSchema extends Data
{
    public static function fromFilename($filename): SwaggerSchema
    {
        $schema = Yaml::parse(file_get_contents($filename));

        return new static($schema);
    }

    public function findRequestPathBreadcrumbsByHttpRequest(Request $request): RequestPathBreadcrumbs
    {
        return new RequestPathBreadcrumbs([
            'paths',
            new RelativePath(parent::get('basePath'), $request->getPathInfo()),
        ]);
    }

    public function findRequestPathSchemaByHttpRequest(Request $request): stdClass
    {
        $breadcrumbs = $this->findRequestPathBreadcrumbsByHttpRequest($request);

        if (!$this->has($breadcrumbs)) {
            throw new Path($breadcrumbs);
        }

        return $this->get($breadcrumbs);
    }

    public function findRequestMethodBreadcrumbsByHttpRequest(Request $request): RequestMethodBreadcrumbs
    {
        $breadcrumbs = $this->findRequestPathBreadcrumbsByHttpRequest($request);
        $breadcrumbs = RequestMethodBreadcrumbs::fromBreadcrumbs($breadcrumbs);
        $breadcrumbs->breadcrumbs[] = strtolower($request->getMethod());

        return $breadcrumbs;
    }

    public function findRequestMethodSchemaByHttpRequest(Request $request): stdClass
    {
        $breadcrumbs = $this->findRequestMethodBreadcrumbsByHttpRequest($request);

        if (!$this->has($breadcrumbs)) {
            throw new Method($breadcrumbs, $this->findRequestPathSchemaByHttpRequest($request));
        }

        return $this->get($breadcrumbs);
    }

    public function findResponsePathBreadcrumbsByHttpResponse(Request $request, Response $response): ResponsePathBreadcrumbs
    {
        $breadcrumbs = $this->findRequestMethodBreadcrumbsByHttpRequest($request);
        $breadcrumbs = ResponsePathBreadcrumbs::fromBreadcrumbs($breadcrumbs);
        $breadcrumbs->breadcrumbs[] = 'responses';
        $breadcrumbs->breadcrumbs[] = $response->getStatusCode();
        $breadcrumbs->breadcrumbs[] = 'schema';

        return $breadcrumbs;
    }

    public function findResponseSchemaByHttpResponse(Request $request, Response $response): stdClass
    {
        $breadcrumbs = $this->findResponsePathBreadcrumbsByHttpResponse($request, $response);

        if (!$this->has($breadcrumbs)) {
            throw new StatusCode($breadcrumbs, $this->findRequestMethodSchemaByHttpRequest($request));
        }

        return $this->get($breadcrumbs);
    }

    public function get($key, $default = null): stdClass
    {
        $ret = parent::get($key, $default);
        $ret = json_decode(json_encode($ret));

        return $ret;
    }
}
