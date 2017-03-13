<?php

namespace Absolvent\swagger;

use Absolvent\swagger\Breadcrumbs\RequestPath\RequestMethod as RequestMethodBreadcrumbs;

class ControllerName
{
    public $breadcrumbs;

    public function __construct(RequestMethodBreadcrumbs $breadcrumbs)
    {
        $this->breadcrumbs = $breadcrumbs;
    }

    public function __toString(): string
    {
        $breadcrumbs = array_map('static::breadcrumbToStudlyCase', $this->breadcrumbs->breadcrumbs);
        $breadcrumbs = array_reverse($breadcrumbs);

        return implode($breadcrumbs);
    }

    private static function breadcrumbToStudlyCase(string $breadcrumb): string
    {
        return studly_case(trim($breadcrumb, '/'));
    }
}
