<?php

namespace Absolvent\swagger\Exception;

use Absolvent\swagger\Breadcrumbs;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class SchemaPartNotFound extends BadRequestHttpException
{
    public $breadcrumbs;

    public function __construct(Breadcrumbs $breadcrumbs)
    {
        $this->breadcrumbs = $breadcrumbs;
        $this->message = strval($breadcrumbs);
    }
}
