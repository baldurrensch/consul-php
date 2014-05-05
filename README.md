Consul.io PHP library
=====================

This is a PHP library for the Consul.io[link] application.

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

Tests
-----

Run the unit and functional tests by running `phpunit` in the root of the repository.
