<?php

define('DS', DIRECTORY_SEPARATOR);
require_once __DIR__ . DS . 'vendor' . DS . 'autoload.php';

use Symfony\Component\Yaml\Yaml;
use \Doctrine\ORM\Tools\Console\ConsoleRunner;

// cli-config.php
$serviceFile = '.' . DS . 'config' . DS . 'services.yaml';


try {
    $services = Yaml::parseFile($serviceFile);

    $dbClass = $serviceFile['doctrineCliConfig']['namespace'];
    $entityManager = $serviceFile['doctrineCliConfig']['entityManager'];
    return ConsoleRunner::createHelperSet((new $dbClass)->$entityManager);
} catch (ParseException $exception) {
    printf('Unable to parse the YAML string: %s', $exception->getMessage());
}


