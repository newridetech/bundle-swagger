<?php

namespace Absolvent\swagger\Exception;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class SchemaPartNotFound extends BadRequestHttpException
{
    public $breadcrumbs;

    public function __construct(array $breadcrumbs)
    {
        parent::__construct();

        $this->breadcrumbs = $breadcrumbs;
    }

    public function getBreadcrumbsPath(): string
    {
        return implode('.', $this->breadcrumbs);
    }
}
