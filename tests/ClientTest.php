<?php

namespace BR\Consul\Test;

use BR\Consul\Client;
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
     * @expectedException \Guzzle\Http\Exception\ClientErrorResponseException
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
