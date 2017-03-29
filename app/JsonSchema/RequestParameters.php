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
        return new ArrayIterator($this->data);
    }
}
