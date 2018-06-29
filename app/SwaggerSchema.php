<?php

namespace Newride\swagger;

use Newride\swagger\Breadcrumbs\RequestPath as RequestPathBreadcrumbs;
use Newride\swagger\Breadcrumbs\RequestPath\RequestMethod as RequestMethodBreadcrumbs;
use Newride\swagger\Breadcrumbs\RequestPath\RequestMethod\ResponsePath as ResponsePathBreadcrumbs;
use Newride\swagger\Breadcrumbs\RequestPath\RequestMethod\RequestParameters as RequestParametersBreadcrumbs;
use Newride\swagger\Exception\SchemaPartNotFound;
use Newride\swagger\JsonSchema\RequestParameters as RequestParametersSchema;
use Dflydev\DotAccessData\Data;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Yaml\Yaml;

class SwaggerSchema extends Data
{
    /**
     * @var string|null for debugging only
     */
    public $filename;

    public static function fromFilename(string $filename): self
    {
        $schema = Yaml::parse(file_get_contents($filename));

        return new static($schema, $filename);
    }

    public static function fromSchema($schema, $filename): self
    {
        return new static(json_decode(json_encode($schema), true), $filename);
    }

    public static function refToPath(string $ref): string
    {
        return str_replace('/', '.', str_replace_first('#/', '', $ref));
    }

    public function __construct($schema, string $filename)
    {
        parent::__construct($schema);

        $this->filename = $filename;
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
            throw new SchemaPartNotFound($breadcrumbs, $this->filename);
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
            throw new SchemaPartNotFound($breadcrumbs, $this->filename);
        }

        return new JsonSchema(parent::get($breadcrumbs));
    }

    public function findRequestParametersBreadcrumbsByHttpRequest(Request $request): RequestParametersBreadcrumbs
    {
        $breadcrumbs = $this->findRequestMethodBreadcrumbsByHttpRequest($request);
        $breadcrumbs = RequestParametersBreadcrumbs::fromBreadcrumbs($breadcrumbs);
        $breadcrumbs->breadcrumbs[] = 'parameters';

        return $breadcrumbs;
    }

    public function findRequestParametersSchemaByHttpRequest(Request $request): RequestParametersSchema
    {
        $breadcrumbs = $this->findRequestParametersBreadcrumbsByHttpRequest($request);

        if (!$this->has($breadcrumbs)) {
            throw new SchemaPartNotFound($breadcrumbs, $this->filename);
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
            throw new SchemaPartNotFound($breadcrumbs, $this->filename);
        }

        return new JsonSchema(parent::get($breadcrumbs));
    }

    public function get($key, $default = null)
    {
        $ret = parent::get($key, $default);
        $ret = json_decode(json_encode($ret));

        return $ret;
    }

    public function getByRef(string $ref)
    {
        return $this->get(self::refToPath($ref));
    }

    public function hasRequestParametersSchemaByHttpRequest(Request $request): bool
    {
        $breadcrumbs = $this->findRequestParametersBreadcrumbsByHttpRequest($request);

        return $this->has($breadcrumbs);
    }
}
