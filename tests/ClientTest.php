<?php

namespace BR\Consul\Test;

use BR\Consul\Client;
use Guzzle\Http\Message\Request;
use Guzzle\Http\Message\Response;
use Guzzle\Plugin\Mock\MockPlugin;

class ClientTest extends \PHPUnit_Framework_TestCase
{
    public function testKeyValueGet()
    {
        $client = new Client('http://localhost:8500');
        $mock = new MockPlugin();
        $mock->addResponse($this->createMockResponse(200, 'get-value_key1'));
        $client->addGuzzlePlugin($mock);

        $result = $client->getValue('key1');
        $this->assertEquals('key1', $result->getKey());
        $this->assertEquals('abc', $result->getValue());
    }

    /**
     * @expectedException \BR\Consul\Exception\NotFoundException
     */
    public function testKeyValueGetNotFound()
    {
        $client = new Client('http://localhost:8500');
        $mock = new MockPlugin();
        $mock->addResponse($this->createMockResponse(404));
        $client->addGuzzlePlugin($mock);

        $result = $client->getValue('key1');
    }

    public function testKeyValueDatacenter()
    {
        $client = new Client('http://localhost:8500');
        $mock = new MockPlugin();
        $mock->addResponse($this->createMockResponse(200, 'get-value_key1'));
        $mock->addResponse($this->createMockResponse(200, 'set-value_key2'));
        $mock->addResponse($this->createMockResponse(200));
        $client->addGuzzlePlugin($mock);

        $client->getValue('key1', 'datacenter1');
        $client->setValue('key1', 'doge', 'datacenter1');
        $client->deleteValue('key1', 'datacenter1');

        /** @var $request Request */
        foreach ($mock->getReceivedRequests() as $request) {
            $this->assertEquals('http://localhost:8500/v1/kv/key1?dc=datacenter1', $request->getUrl());
        }
    }

    public function testKeyValueSet()
    {
        $client = new Client('http://localhost:8500');
        $mock = new MockPlugin();
        $mock->addResponse($this->createMockResponse(200, 'set-value_key2'));
        $client->addGuzzlePlugin($mock);

        $result = $client->setValue('key2', 'doge');

        $this->assertTrue($result);
    }

    /**
     * @expectedException \BR\Consul\Exception\NotFoundException
     */
    public function testKeyValueDelete()
    {
        $client = new Client('http://localhost:8500');
        $mock = new MockPlugin();
        $mock->addResponse($this->createMockResponse(200));
        $mock->addResponse($this->createMockResponse(404));
        $client->addGuzzlePlugin($mock);

        $client->deleteValue('key1');

        $client->getValue('key1');
    }

    protected function createMockResponse($status = 200, $contentName = null)
    {
        $content = $contentName ? file_get_contents(__DIR__ . '/fixtures/' . $contentName) : null;

        $response = new Response(
            $status,
            [
                'Content-Type' => 'application/json',
                'X-Consul-Index' => 410,
                'X-Consul-Knownleader' => 'true',
                'X-Consul-Lastcontact' => 0,
            ],
            $content
        );

        return $response;
    }
}
