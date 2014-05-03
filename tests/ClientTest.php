<?php

namespace BR\Consul\Test;

use BR\Consul\Client;

class ClientTest extends \PHPUnit_Framework_TestCase
{
    public function testKeyValueSet()
    {
        $client = new Client('localhost');

    }
}
