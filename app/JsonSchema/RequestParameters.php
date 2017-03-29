<?php

namespace Absolvent\swagger\JsonSchema;

use Absolvent\swagger\JsonSchema;
use ArrayIterator;
use Iterator;
use IteratorAggregate;
use stdClass;

class RequestParameters extends JsonSchema implements IteratorAggregate
{
    public function getIterator(): Iterator
    {
        $data = array_map(function (stdClass $item) {
            return json_decode(json_encode($item));
        }, $this->data);

        return new ArrayIterator($data);
    }
}
