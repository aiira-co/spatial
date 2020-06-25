<?php

define('DS', DIRECTORY_SEPARATOR);
require_once __DIR__ . DS . 'vendor' . DS . 'autoload.php';

use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;
use \Doctrine\ORM\Tools\Console\ConsoleRunner;

// cli-config.php
$configDir = '.' . DS . 'config' . DS;

try {
//    config/service.yml
    $services = Yaml::parseFile($configDir . 'services.yaml');
    define('SpatialServices', $services);
//    config/packages/doctrine.yaml
    $doctrineConfigs = Yaml::parseFile($configDir . DS . 'packages' . DS . 'doctrine.yaml');
    define('DoctrineConfig', $doctrineConfigs);

    $dbClass = ((array)$doctrineConfigs)['cli.config']['namespace'];
    $entityManager = $doctrineConfigs['cli.config']['entityManager'];

    return ConsoleRunner::createHelperSet((new $dbClass())->{$entityManager});
} catch (ParseException $exception) {
    printf('Unable to parse the YAML string: %s', $exception->getMessage());
}


