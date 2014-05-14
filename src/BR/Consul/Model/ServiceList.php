<?php

namespace BR\Consul\Model;

use Guzzle\Service\Command\OperationCommand;
use Guzzle\Service\Command\ResponseClassInterface;

class ServiceList implements ResponseClassInterface, \ArrayAccess, \Countable
{
    /**
     * @var Service[]
     */
    private $services = [];

    /**
     * {@inheritdoc}
     */
    public static function fromCommand(OperationCommand $command)
    {
        $response = json_decode($command->getResponse()->getBody(true), true);

        $services = [];

        foreach ($response as $serviceDescription) {
            $service = new Service();
            $service->setId($serviceDescription['ID']);
            $service->setName($serviceDescription['Service']);
            $service->setTags($serviceDescription['Tags']);
            $service->setPort($serviceDescription['Port']);

            $services[] = $service;
        }

        return new self($services);
    }

    public function __construct(array $services)
    {
        $this->services = $services;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->services);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        return $this->services[$offset];
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value)
    {
        $this->services[$offset] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
        unset($this->services[$offset]);
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return count($this->services);
    }
}
