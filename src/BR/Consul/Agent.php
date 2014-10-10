<?php

namespace BR\Consul;

use BR\Consul\Model\DatacenterList;
use BR\Consul\Model\Service;
use BR\Consul\Model\ServiceList;

class Agent extends AbstractClient
{
    /**
     * Registers a service with the local agent.
     *
     * @link http://www.consul.io/docs/agent/http.html#toc_15
     *
     * @param  Service $service
     * @return boolean
     */
    public function registerService(Service $service)
    {
        $command = $this->client->getCommand(
            'AgentServiceRegister',
            [
                'Name' => $service->getName(),
                'ID' => $service->getId(),
                'Port' => $service->getPort(),
                'Tags' => $service->getTags(),
            ]
        );
        $result = $command->execute();

        return $result->isSuccessful();
    }

    /**
     * Returns a list of services that are currently registered with the local agent
     *
     * @link http://www.consul.io/docs/agent/http.html#toc_6
     *
     * @return ServiceList
     */
    public function getServices()
    {
        $command = $this->client->getCommand(
            'AgentGetServices'
        );
        $result = $command->execute();

        return $result;
    }

    /**
     * Removes a service from the local agent.
     *
     * @link http://www.consul.io/docs/agent/http.html#toc_16
     *
     * @param  string $serviceId
     * @return boolean
     */
    public function deregisterService($serviceId)
    {
        $command = $this->client->getCommand('AgentServiceDeregister', ['serviceId' => $serviceId]);

        $result = $command->execute();

        return $result->isSuccessful();
    }

    /**
     * Removes a service from the local agent.
     *
     * @link http://www.consul.io/docs/agent/http.html#toc_16
     * @see Client::deregisterService()
     *
     * @param Service $service
     * @return bool
     */
    public function removeService(Service $service)
    {
        return $this->deregisterService($service->getId());
    }

    /**
     * Retrieves the list of datacenters known by Consul.
     *
     * @link http://www.consul.io/docs/agent/http.html#toc_26
     *
     * @return DatacenterList
     */
    public function getDatacenters()
    {
        $command = $this->client->getCommand('AgentCatalogGetDatacenters');

        $result = $command->execute();

        return $result;
    }
} 
