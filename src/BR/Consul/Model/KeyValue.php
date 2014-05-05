<?php

namespace BR\Consul\Model;

use Guzzle\Service\Command\OperationCommand;
use Guzzle\Service\Command\ResponseClassInterface;

class KeyValue implements ResponseClassInterface
{
    /**
     * @var string
     */
    protected $key;

    /**
     * @var mixed
     */
    protected $value;

    /**
     * @var string[]
     */
    protected $flags;

    /**
     * @var int
     */
    protected $createIndex;

    /**
     * @var int
     */
    protected $modifyIndex;

    /**
     * {@inheritdoc}
     */
    public static function fromCommand(OperationCommand $command)
    {
        $response = json_decode($command->getResponse()->getBody(true), true);

        $keyValue = new self();
        $keyValue->createIndex = $response[0]['CreateIndex'];
        $keyValue->modifyIndex = $response[0]['ModifyIndex'];
        $keyValue->key = $response[0]['Key'];
        $keyValue->flags = $response[0]['Flags'];
        $keyValue->value = base64_decode($response[0]['Value']);

        return $keyValue;
    }

    public function getKey()
    {
        return $this->key;
    }

    public function getValue()
    {
        return $this->value;
    }
}
