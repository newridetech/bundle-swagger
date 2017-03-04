<?php

namespace Absolvent\swagger\tests\Unit;

use Absolvent\swagger\RelativePath;
use Absolvent\swagger\tests\TestCase;

class RelativePathTest extends TestCase
{
    public function testThatRelativePathIsGenerated()
    {
        $relativePath = new RelativePath('/app', '/app/pet');
        $this->assertEquals('/pet', strval($relativePath));
    }
}
