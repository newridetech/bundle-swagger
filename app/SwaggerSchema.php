<?php

namespace Absolvent\swagger;

use Absolvent\swagger\Breadcrumbs\RequestPath as RequestPathBreadcrumbs;
use Absolvent\swagger\Breadcrumbs\RequestPath\RequestMethod as RequestMethodBreadcrumbs;
use Absolvent\swagger\Breadcrumbs\RequestPath\RequestMethod\ResponsePath as ResponsePathBreadcrumbs;
use Absolvent\swagger\Breadcrumbs\RequestPath\RequestMethod\Requestparameters as RequestparametersBreadcrumbs;
use Absolvent\swagger\Exception\SchemaPartNotFound\Method;
use Absolvent\swagger\Exception\SchemaPartNotFound\Parameters;
use Absolvent\swagger\Exception\SchemaPartNotFound\Path;
use Absolvent\swagger\Exception\SchemaPartNotFound\StatusCode;
use Absolvent\swagger\JsonSchema\RequestParameters as RequestParametersSchema;
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

    public static function fromSchema(stdClass $schema): SwaggerSchema
    {
        return new static(json_decode(json_encode($schema), true));
    }

    public function findRequestPathBreadcrumbsByHttpRequest(Request $request): RequestPathBreadcrumbs
    {
        return new RequestPathBreadcrumbs([
            'paths',
            new RelativePath(parent::get('basePath'), $request->getPathInfo()),
        ]);
    }

    public function findRequestPathSchemaByHttpRequest(Request $request): JsonSchema
    {
        $breadcrumbs = $this->findRequestPathBreadcrumbsByHttpRequest($request);

        if (!$this->has($breadcrumbs)) {
            throw new Path($breadcrumbs);
        }

        return new JsonSchema(parent::get($breadcrumbs));
    }

    public function findRequestMethodBreadcrumbsByHttpRequest(Request $request): RequestMethodBreadcrumbs
    {
        $breadcrumbs = $this->findRequestPathBreadcrumbsByHttpRequest($request);
        $breadcrumbs = RequestMethodBreadcrumbs::fromBreadcrumbs($breadcrumbs);
        $breadcrumbs->breadcrumbs[] = strtolower($request->getMethod());

        return $breadcrumbs;
    }

    public function findRequestMethodSchemaByHttpRequest(Request $request): JsonSchema
    {
        $breadcrumbs = $this->findRequestMethodBreadcrumbsByHttpRequest($request);

        if (!$this->has($breadcrumbs)) {
            throw new Method($breadcrumbs, $this->findRequestPathSchemaByHttpRequest($request));
        }

        return new JsonSchema(parent::get($breadcrumbs));
    }

    public function findRequestParametersBreadcrumbsByHttpRequest(Request $request): RequestParametersBreadcrumbs
    {
        $breadcrumbs = $this->findRequestMethodBreadcrumbsByHttpRequest($request);
        $breadcrumbs = RequestparametersBreadcrumbs::fromBreadcrumbs($breadcrumbs);
        $breadcrumbs->breadcrumbs[] = 'parameters';

        return $breadcrumbs;
    }

    public function findRequestParametersSchemaByHttpRequest(Request $request): RequestParametersSchema
    {
        $breadcrumbs = $this->findRequestParametersBreadcrumbsByHttpRequest($request);

        if (!$this->has($breadcrumbs)) {
            throw new Parameters($breadcrumbs, $this->findRequestMethodSchemaByHttpRequest($request));
        }

        return new RequestParametersSchema($this->get($breadcrumbs));
    }

    public function findResponsePathBreadcrumbsByHttpResponse(Request $request, Response $response): ResponsePathBreadcrumbs
    {
        return $this->findResponsePathBreadcrumbsByHttpResponseStatusCode($request, $response, $response->getStatusCode());
    }

    public function findResponsePathBreadcrumbsByHttpResponseDefault(Request $request, Response $response): ResponsePathBreadcrumbs
    {
        return $this->findResponsePathBreadcrumbsByHttpResponseStatusCode($request, $response, 'default');
    }

    public function findResponsePathBreadcrumbsByHttpResponseStatusCode(Request $request, Response $response, $statusCode): ResponsePathBreadcrumbs
    {
        $breadcrumbs = $this->findRequestMethodBreadcrumbsByHttpRequest($request);
        $breadcrumbs = ResponsePathBreadcrumbs::fromBreadcrumbs($breadcrumbs);
        $breadcrumbs->breadcrumbs[] = 'responses';
        $breadcrumbs->breadcrumbs[] = $statusCode;
        $breadcrumbs->breadcrumbs[] = 'schema';

        return $breadcrumbs;
    }

    public function findResponseSchemaByHttpResponse(Request $request, Response $response): JsonSchema
    {
        $breadcrumbs = $this->findResponsePathBreadcrumbsByHttpResponse($request, $response);
        if (!$this->has($breadcrumbs)) {
            $breadcrumbs = $this->findResponsePathBreadcrumbsByHttpResponseDefault($request, $response);
        }
        if (!$this->has($breadcrumbs)) {
            throw new StatusCode($breadcrumbs, $this->findRequestMethodSchemaByHttpRequest($request));
        }

        return new JsonSchema(parent::get($breadcrumbs));
    }

    public function get($key, $default = null)
    {
        $ret = parent::get($key, $default);
        $ret = json_decode(json_encode($ret));

        return $ret;
    }
}
