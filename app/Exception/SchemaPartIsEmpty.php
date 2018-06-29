<?php

namespace Newride\swagger\Exception;

use Exception;
use Newride\swagger\Breadcrumbs;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class SchemaPartIsEmpty extends BadRequestHttpException
{
    public $breadcrumbs;
    public $filename;

    public function __construct(Breadcrumbs $breadcrumbs, string $filename, Exception $previous = null, int $code = 0)
    {
        parent::__construct(static::createMessage($breadcrumbs, $filename), $previous, $code);

        $this->breadcrumbs = $breadcrumbs;
    }

    private static function createMessage(Breadcrumbs $breadcrumbs, string $filename): string
    {
        return $filename.': "'.strval($breadcrumbs).'" property is empty.';
    }
}
