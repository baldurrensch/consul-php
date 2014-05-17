<?php

namespace BR\Consul\Test\Model;

use BR\Consul\Model\ServiceList;

class ServiceListTest extends \PHPUnit_Framework_TestCase
{
    public function testOffsetExists()
    {
        $serviceList = new ServiceList([0 => true, 1 => true, 2 => true]);

        $this->assertTrue($serviceList->offsetExists(1));
        $this->assertFalse($serviceList->offsetExists(4));
    }

    public function testOffsetSet()
    {
        $serviceList = new ServiceList([0 => true, 1 => true, 2 => true]);

        $serviceList->offsetSet(0, 'new value');
        $this->assertEquals('new value', $serviceList[0]);

        $serviceList->offsetSet(4, 'new value');
        $this->assertEquals('new value', $serviceList[4]);
    }

    public function testOffsetUnset()
    {
        $serviceList = new ServiceList([0 => true, 1 => true, 2 => true]);

        $serviceList->offsetUnset(0);
        $this->assertArrayNotHasKey(0, $serviceList);
    }
} 
