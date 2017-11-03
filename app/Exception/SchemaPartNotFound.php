<?php

namespace Newride\swagger\Exception;

use Newride\swagger\Breadcrumbs;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class SchemaPartNotFound extends BadRequestHttpException
{
    public $breadcrumbs;
    public $filename;

    public function __construct(Breadcrumbs $breadcrumbs, string $filename)
    {
        $this->breadcrumbs = $breadcrumbs;
        $this->message = static::createMessage($breadcrumbs, $filename);
    }

    private static function createMessage(Breadcrumbs $breadcrumbs, string $filename): string
    {
        return $filename.': "'.strval($breadcrumbs).'" property is missing.';
    }
}
