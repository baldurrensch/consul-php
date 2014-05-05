<?php

namespace BR\Consul;

use BR\Consul\Model\KeyValue;
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
     * @param  string   $key
     * @return KeyValue
     */
    public function getValue($key)
    {
        $command = $this->client->getCommand('GetValue', ['key' => $key]);
        $result = $command->execute();

        return $result;
    }

    /**
     * @param  string $key
     * @param  string $value
     * @return mixed
     */
    public function setValue($key, $value)
    {
        $command = $this->client->getCommand('SetValue', ['key' => $key, 'value' => $value]);
        $command->prepare();

        $result = $command->execute();

        return $result;
    }

    /**
     * @param  string $key
     * @return mixed
     */
    public function deleteValue($key)
    {
        $command = $this->client->getCommand('DeleteValue', ['key' => $key]);
        $result = $command->execute();

        return $result;
    }

    /**
     * @param EventSubscriberInterface $plugin
     */
    public function addGuzzlePlugin(EventSubscriberInterface $plugin)
    {
        $this->client->addSubscriber($plugin);
    }
} 
