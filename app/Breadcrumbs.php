<?php

namespace Absolvent\swagger;

class Breadcrumbs
{
    public $breadcrumbs;

    public function __construct(array $breadcrumbs)
    {
        $this->breadcrumbs = $breadcrumbs;
    }

    public function __toString(): string
    {
        return $this->getBreadcrumbsPath();
    }

    public function getBreadcrumbsPath(): string
    {
        return implode('.', $this->breadcrumbs);
    }
}
