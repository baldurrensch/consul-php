<?php

namespace BR\Consul;

use BR\Consul\Exception\NotFoundException;
use BR\Consul\Model\KeyValue;
use BR\Consul\Model\Service;
use Guzzle\Http\Exception\ClientErrorResponseException;
use Guzzle\Http\Message\Response;
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
     * @param EventSubscriberInterface $plugin
     */
    public function addGuzzlePlugin(EventSubscriberInterface $plugin)
    {
        $this->client->addSubscriber($plugin);
    }
} 
