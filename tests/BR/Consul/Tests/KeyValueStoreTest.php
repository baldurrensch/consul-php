<?php

namespace BR\Consul\Tests;

use BR\Consul\KeyValueStore;
use Guzzle\Plugin\Mock\MockPlugin;

class KeyValueStoreTest extends ClientTest
{
    public function testKeyValueGet()
    {
        $client = new KeyValueStore('http://localhost:8500');
        $mock = new MockPlugin();
        $mock->addResponse(__DIR__ . '/fixtures/get-value_key1');
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
        $client = new KeyValueStore('http://localhost:8500');
        $mock = new MockPlugin();
        $mock->addResponse($this->createMockResponse(404));
        $client->addGuzzlePlugin($mock);

        $client->getValue('key1');
    }

    public function testKeyValueDatacenter()
    {
        $client = new KeyValueStore('http://localhost:8500');
        $mock = new MockPlugin();
        $mock->addResponse(__DIR__ . '/fixtures/get-value_key1');
        $mock->addResponse(__DIR__ . '/fixtures/set-value_key2');
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
        $client = new KeyValueStore('http://localhost:8500');
        $mock = new MockPlugin();
        $mock->addResponse(__DIR__ . '/fixtures/set-value_key2');
        $client->addGuzzlePlugin($mock);

        $result = $client->setValue('key2', 'doge');

        $this->assertTrue($result);
    }

    /**
     * @expectedException \BR\Consul\Exception\NotFoundException
     */
    public function testKeyValueDelete()
    {
        $client = new KeyValueStore('http://localhost:8500');
        $mock = new MockPlugin();
        $mock->addResponse($this->createMockResponse(200));
        $mock->addResponse($this->createMockResponse(404));
        $client->addGuzzlePlugin($mock);

        $client->deleteValue('key1');

        $client->getValue('key1');
    }
} 
