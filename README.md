Consul.io PHP library
=====================

[![Build Status](https://travis-ci.org/baldurrensch/consul-php.svg?branch=master)](https://travis-ci.org/baldurrensch/consul-php)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/baldurrensch/consul-php/badges/quality-score.png?s=47ccfd304909099f4a5e1241dc3d30db8f8d0134)](https://scrutinizer-ci.com/g/baldurrensch/consul-php/)
[![Code Coverage](https://scrutinizer-ci.com/g/baldurrensch/consul-php/badges/coverage.png?s=2aaa77c2ffbd7d5332a43d04f81216f7b32d1cb5)](https://scrutinizer-ci.com/g/baldurrensch/consul-php/)

This is a PHP library for the [consul.io] application. Note that this is in development right now. Feel free to open PRs.

Installation
------------

1. Require the package via composer

```bash
$ composer require "br/consul-php"
```

2. Instantiate the library:

Depending on which part of the library you want to use, instantiate the correct Client:

```php
$client = new \BR\Consul\Agent('http://localhost:8500'); // to issue commands to the local agent
$client = new \BR\Consul\KeyValueStore('http://localhost:8500'); // to access the Key Value Store
```

Usage
-----

### 1. Key/Value Store

Use the `getValue()`, `setValue()` and `deleteValue()` functions of the client. Example:

```php
$client->setValue('myKey', 'myValue');
$client->setValue('myKey', 'myValue', 'datacenter-east'); // Set the value in the datacenter-east datacenter

$client->getValue('myKey');
$client->getValue('myKey', 'datacenter-east'); // Get the value from the datacenter-east datacenter

$client->deleteValue('myKey');
$client->deleteValue('myKey', 'datacenter-east'); // Delete the value from the datacenter-east datacenter
```

### 2. Agent

#### 2.1 Registering a service with the agent

In order to register a service, you have to create a `BR\Consul\Model\Service` object, and pass it to the
`Client::registerService()` function. Example:

```php
$service = new \BR\Consul\Model\Service();
$service->setName('postgres');
$service->setId('postgres-1');
$service->setTags(['master']);
$service->setPort(5432);

$success = $client->registerService($service);
```

#### 2.2 Retrieving a list of all services registered with the agent

You can retrieve a list of services that are registered with the agent. Example:

```php
$services = $service->getServices();
var_dump(count($services));

/** $srv \BR\Consul\Model\Service */
foreach ($services as $srv) {
    echo $srv->getName()
}
```

#### 2.3 Removing a service from the agent

```php
$client->deregisterService('postgres-1');
// alternatively
$client->removeService($service);
```

Tests
-----

Run the unit and functional tests by running `phpunit` in the root of the repository.

[consul.io]: http://www.consul.io/

To run consul, use: 

```bash
$ ./consul agent -data-dir . --bootstrap -server
```
