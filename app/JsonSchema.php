<?php

namespace Newride\swagger;

use Dflydev\DotAccessData\Data;

class JsonSchema extends Data
{
    public function export()
    {
        return json_decode(json_encode(parent::export()));
    }
}
