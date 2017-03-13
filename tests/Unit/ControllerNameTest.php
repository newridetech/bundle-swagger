<?php

namespace Absolvent\swagger\tests\Unit;

use Absolvent\swagger\Breadcrumbs\RequestPath\RequestMethod as RequestMethodBreadcrumbs;
use Absolvent\swagger\ControllerName;
use Absolvent\swagger\tests\TestCase;

class ControllerNameTest extends TestCase
{
    public function testThatControllerNameIsGenerated()
    {
        $breadcrumbs = new RequestMethodBreadcrumbs([
            '/pet',
            'get',
        ]);
        $ControllerName = new ControllerName($breadcrumbs);
        $this->assertSame('GetPet', strval($ControllerName));
    }
}
