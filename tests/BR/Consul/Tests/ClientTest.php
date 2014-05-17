<?php

namespace BR\Consul\Tests;

use BR\Consul\Client;
use BR\Consul\Model\Service;
use Guzzle\Http\Message\Request;
use Guzzle\Http\Message\Response;
use Guzzle\Plugin\Mock\MockPlugin;

class ClientTest extends \PHPUnit_Framework_TestCase
{
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
}
