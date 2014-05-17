<?php

namespace BR\Consul;

use BR\Consul\Exception\NotFoundException;
use BR\Consul\Model\KeyValue;
use BR\Consul\Model\Service;
use BR\Consul\Model\ServiceList;
use Guzzle\Http\Exception\ClientErrorResponseException;
use Guzzle\Service\Client as GuzzleClient;
use Guzzle\Service\Description\ServiceDescription;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class Client
{
    /**
     * @var GuzzleClient
     */
    protected $client;

    /**
     * @param string $baseUrl
     */
    public function __construct($baseUrl)
    {
        $this->client = new GuzzleClient($baseUrl);
        $this->client->setDescription(ServiceDescription::factory(__DIR__ . '/config/service.json'));
    }

    /**
     * Gets a value from the key/value store
     *
     * @link http://www.consul.io/docs/agent/http.html#toc_3
     *
     * @param  string            $key
     * @param  string|null       $datacenter
     * @throws NotFoundException
     * @return KeyValue
     */
    public function getValue($key, $datacenter = null)
    {
        $command = $this->client->getCommand('GetValue', ['key' => $key, 'datacenter' => $datacenter,]);
        try {
            $result = $command->execute();
        } catch (ClientErrorResponseException $e) {
            throw new NotFoundException();
        }

        return $result;
    }

    /**
     * Sets a value in the key/value store
     *
     * @link http://www.consul.io/docs/agent/http.html#toc_3
     *
     * @param  string      $key
     * @param  string      $value
     * @param  string|null $datacenter
     * @return mixed
     */
    public function setValue($key, $value, $datacenter = null)
    {
        $command = $this->client->getCommand(
            'SetValue',
            [
                'key' => $key,
                'value' => $value,
                'datacenter' => $datacenter,
            ]
        );
        $command->prepare();

        $result = $command->execute();

        return $result;
    }

    /**
     * Deletes a value from the key/value store.
     *
     * @link http://www.consul.io/docs/agent/http.html#toc_3
     *
     * @param  string      $key
     * @param  string|null $datacenter
     * @return mixed
     */
    public function deleteValue($key, $datacenter = null)
    {
        $command = $this->client->getCommand('DeleteValue', ['key' => $key, 'datacenter' => $datacenter,]);
        $result = $command->execute();

        return $result;
    }

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
     * @param EventSubscriberInterface $plugin
     */
    public function addGuzzlePlugin(EventSubscriberInterface $plugin)
    {
        $this->client->addSubscriber($plugin);
    }
} 
