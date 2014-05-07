Consul.io PHP library
=====================

[![Build Status](https://travis-ci.org/baldurrensch/consul-php.svg?branch=master)](https://travis-ci.org/baldurrensch/consul-php)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/baldurrensch/consul-php/badges/quality-score.png?s=47ccfd304909099f4a5e1241dc3d30db8f8d0134)](https://scrutinizer-ci.com/g/baldurrensch/consul-php/)

This is a PHP library for the [consul.io] application.

Installation
------------

1. Require the package via composer

```bash
$ composer require "br/consul-php"
```

2. Instantiate the library:

```php
$client = new \BR\Consul\Client('http://localhost:8500');
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
$service->setId('postegres-1');
$service->setTags(['master']);
$service->setPort(5432);

$success = $client->registerService($service);
```

Tests
-----

Run the unit and functional tests by running `phpunit` in the root of the repository.

[consul.io]: http://www.consul.io/
