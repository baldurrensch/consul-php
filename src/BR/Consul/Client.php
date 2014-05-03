<?php

namespace BR\Consul;

use Guzzle\Service\Client as GuzzleClient;
use Guzzle\Service\Description\ServiceDescription;

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
} 
