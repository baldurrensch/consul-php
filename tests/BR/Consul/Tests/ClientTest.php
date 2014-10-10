<?php

namespace BR\Consul\Tests;

use BR\Consul\Agent;
use BR\Consul\Client;
use BR\Consul\Model\Datacenter;
use BR\Consul\Model\DatacenterList;
use Guzzle\Http\Message\Response;
use Guzzle\Plugin\Mock\MockPlugin;

class ClientTest extends \PHPUnit_Framework_TestCase
{
    public function testGetDatacenters()
    {
        $client = new Agent('http://localhost:8500');

        $mock = new MockPlugin();
        $mock->addResponse(__DIR__ . '/fixtures/get-datacenters');
        $client->addGuzzlePlugin($mock);

        $response = $client->getDatacenters();

        $expectedDcList = new DatacenterList(
            [
                new Datacenter('dc1')
            ]
        );

        $this->assertEquals($expectedDcList, $response);
    }

    protected function createMockResponse($status = 200)
    {
        $response = new Response(
            $status,
            [
                'Content-Type' => 'application/json',
                'X-Consul-Index' => 410,
                'X-Consul-Knownleader' => 'true',
                'X-Consul-Lastcontact' => 0,
            ]
        );

        return $response;
    }
}
