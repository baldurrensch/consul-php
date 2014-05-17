<?php

namespace BR\Consul;

use BR\Consul\Exception\NotFoundException;
use BR\Consul\Model\KeyValue;
use Guzzle\Http\Exception\ClientErrorResponseException;

class KeyValueStore extends AbstractClient
{
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
        $command = $this->client->getCommand(
            'DeleteValue',
            [
                'key' => $key,
                'datacenter' => $datacenter,
            ]
        );
        $result = $command->execute();

        return $result;
    }
} 
