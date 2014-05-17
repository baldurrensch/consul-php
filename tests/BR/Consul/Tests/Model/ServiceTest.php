<?php

namespace BR\Consul\Tests\Model;

use BR\Consul\Model\Service;

class ServiceTest extends \PHPUnit_Framework_TestCase
{
    public function testAddTag()
    {
        $service = new Service();

        $service->addTag('abc');
        $this->assertEquals(['abc'], $service->getTags());

        $service->addTag('def');
        $this->assertEquals(['abc', 'def'], $service->getTags());
    }
} 
