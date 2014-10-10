<?php

namespace BR\Consul\Model;

use Guzzle\Service\Command\OperationCommand;
use Guzzle\Service\Command\ResponseClassInterface;

class DatacenterList implements ResponseClassInterface, \ArrayAccess, \Countable
{
    /**
     * @var Datacenter[]
     */
    private $datacenters;

    /**
     * {@inheritdoc}
     */
    public static function fromCommand(OperationCommand $command)
    {
        $datacenters = [];
        $response = json_decode($command->getResponse()->getBody(true), true);

        foreach ($response as $part) {
            $datacenters[] = new Datacenter($part);
        }

        return new self($datacenters);
    }

    public function __construct(array $datacenters)
    {
        $this->datacenters = $datacenters;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->datacenters);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        return $this->datacenters[$offset];
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value)
    {
        $this->datacenters[$offset] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
        unset($this->datacenters[$offset]);
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return count($this->datacenters);
    }
} 
