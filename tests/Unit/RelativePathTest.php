<?php

namespace Absolvent\swagger\tests\Unit;

use Absolvent\swagger\RelativePath;
use PHPUnit\Framework\TestCase;

class RelativePathTest extends TestCase
{
    public function testThatRelativePathIsGenerated()
    {
        $relativePath = new RelativePath('/app', '/app/pet');
        self::assertEquals('/pet', strval($relativePath));
    }
}
