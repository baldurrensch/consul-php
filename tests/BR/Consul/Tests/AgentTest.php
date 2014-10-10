<?php

namespace BR\Consul\Tests;

use BR\Consul\Agent;
use BR\Consul\Model\Datacenter;
use BR\Consul\Model\DatacenterList;
use BR\Consul\Model\Service;
use Guzzle\Plugin\Mock\MockPlugin;

class AgentTest extends ClientTest
{
    /**
     * @dataProvider getTestServices
     */
    public function testServiceRegister(Service $service, $expectedRequest)
    {
        $client = new Agent('http://localhost:8500');

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
        $client = new Agent('http://localhost:8500');

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

    public function testServiceDeregister()
    {
        $client = new Agent('http://localhost:8500');

        $mock = new MockPlugin();
        $mock->addResponse(__DIR__ . '/fixtures/service-deregister');
        $client->addGuzzlePlugin($mock);

        $response = $client->deregisterService('abc');

        $this->assertTrue($response);
    }

    public function testServiceRemove()
    {
        $client = new Agent('http://localhost:8500');

        $mock = new MockPlugin();
        $mock->addResponse(__DIR__ . '/fixtures/service-deregister');
        $client->addGuzzlePlugin($mock);

        $service = new Service();
        $service->setId('abc');

        $response = $client->removeService($service);

        $this->assertTrue($response);
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
