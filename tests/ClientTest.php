<?php

namespace BR\Consul\Test;

use BR\Consul\Client;
use BR\Consul\Model\Service;
use Guzzle\Http\Message\Request;
use Guzzle\Http\Message\Response;
use Guzzle\Plugin\Mock\MockPlugin;

class ClientTest extends \PHPUnit_Framework_TestCase
{
    public function testKeyValueGet()
    {
        $client = new Client('http://localhost:8500');
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
        $client = new Client('http://localhost:8500');
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
        $client = new Client('http://localhost:8500');
        $mock = new MockPlugin();
        $mock->addResponse($this->createMockResponse(200));
        $mock->addResponse($this->createMockResponse(404));
        $client->addGuzzlePlugin($mock);

        $client->deleteValue('key1');

        $client->getValue('key1');
    }

    /**
     * @dataProvider getTestServices
     */
    public function testServiceRegister(Service $service, $expectedRequest)
    {
        $client = new Client('http://localhost:8500');

        $mock = new MockPlugin();
        $mock->readBodies(true);
        $mock->addResponse(__DIR__ . '/fixtures/service-register');
        $client->addGuzzlePlugin($mock);

        $result = $client->registerService($service);

        /** @var $request \Guzzle\Http\Message\EntityEnclosingRequest */
        $request = current($mock->getReceivedRequests());
        $this->assertEquals($expectedRequest, $request->getBody()->__toString(), 'Sent correct request');

        $this->assertTrue($result, 'Returns correct response');
    }

    public function getTestServices()
    {
        $testCases = [];

        $baseService = new Service();
        $baseService->setName('test');

        $testCases[] = [$baseService, '{"Name":"test"}'];

        $service = clone $baseService;
        $service->setPort(8080);
        $testCases[] = [$service, '{"Name":"test","Port":8080}'];

        $service = clone $baseService;
        $service->setId('abc');
        $testCases[] = [$service, '{"Name":"test","ID":"abc"}'];

        $service = clone $baseService;
        $service->setTags(['abc']);
        $testCases[] = [$service, '{"Name":"test","Tags":["abc"]}'];

        $service = clone $baseService;
        $service->setId('abc');
        $service->setTags(['abc']);
        $service->addTag('def');
        $service->setPort(8080);
        $testCases[] = [$service, '{"Name":"test","ID":"abc","Port":8080,"Tags":["abc","def"]}'];

        return $testCases;
    }

    public function testGetServices()
    {
        $client = new Client('http://localhost:8500');

        $mock = new MockPlugin();
        $mock->addResponse(__DIR__ . '/fixtures/services-get');
        $client->addGuzzlePlugin($mock);

        $response = $client->getServices();
        $this->assertCount(2, $response, 'correct number of services');

        $expectedServices = $this->getExpectedServices();

        for ($i = 0; $i < count($expectedServices); $i++) {
            $this->assertService($response[$i], $expectedServices[$i]);
        }
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

    /**
     * @return array
     */
    private function getExpectedServices()
    {
        $expectedServices = [
            [
                'name' => 'test',
                'id' => 'abc',
                'port' => 8080,
                'tags' => ['abc', 'def'],
            ],
            [
                'name' => 'test',
                'id' => 'test',
                'port' => 0,
                'tags' => ['abc',],
            ],
        ];

        return $expectedServices;
    }

    private function assertService(Service $service, $expectedService)
    {
        $this->assertEquals($expectedService['name'], $service->getName(), 'correct name');
        $this->assertEquals($expectedService['id'], $service->getId(), 'correct id');
        $this->assertEquals($expectedService['port'], $service->getPort(), 'correct port');
        $this->assertEquals($expectedService['tags'], $service->getTags(), 'correct tags');
    }
}
