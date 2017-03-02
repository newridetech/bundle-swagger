<?php

namespace Absolvent\swagger;

use stdClass;
use Symfony\Component\Yaml\Yaml;

class SwaggerSchema
{
    public $schema;

    public static function fromFilename($filename): SwaggerSchema
    {
        $schema = Yaml::parse(file_get_contents($filename));
        $schema = json_decode(json_encode($schema));

        return new static($schema);
    }

    public function __construct(stdClass $schema)
    {
        $this->schema = $schema;
    }
}
