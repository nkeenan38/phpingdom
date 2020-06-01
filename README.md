Pingdom REST API
================

A PHP library for the [Pingdom API](https://www.pingdom.com/api/2.1/).

Installation
============

Install the library using composer by adding the following to `composer.json` in the root of your project:

``` javascript
{ 
    "require": {
        "nkeenan38/phppingdom": "1.0.*"
    }
}
```

Use the generated `vendor/.composer/autoload.php` file to autoload the library classes.

Basic usage
===================

You will need a Pingdom application key for authorizatioon. Follow their documentation to generate an application key inside the Pingdom control panel.

```php
<?php

$token    = ''; // Pingdom application key (32 characters)

$pingdom = new \Pingdom\Client($username, $password, $token);

// List of probe servers
$probes = $pingdom->getProbes();
foreach ($probes as $probe) {
    echo $probe->getName() . PHP_EOL;
}

// List of checks
$checks  = $pingdom->getChecks();
foreach ($checks as $check) {
    $results = $pingdom->getResults($check['id']);
}
```