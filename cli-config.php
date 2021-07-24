<?php

const DS = DIRECTORY_SEPARATOR;
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


    //    config/packages/framework.yaml
    $appConfigs = Yaml::parseFile($configDir . DS . 'packages' . DS . 'framework.yaml');
    define('AppConfig', $appConfigs);

    $isProdMode = $appConfigs['enableProdMode'];
//    config/packages/doctrine.yaml
    $doctrineConfigs = Yaml::parseFile(
        $configDir . DS . 'packages' . DS . ('doctrine.yaml')
    );
    $doctrineConfigs = resolveEnv($doctrineConfigs);
    define('DoctrineConfig', $doctrineConfigs);

    $dbClass = ((array)$doctrineConfigs)['cli.config']['namespace'];
    $entityManager = $doctrineConfigs['cli.config']['entityManager'];

    return ConsoleRunner::createHelperSet((new $dbClass())->{$entityManager});
} catch (ParseException $exception) {
    printf('Unable to parse the YAML string: %s', $exception->getMessage());
}


function resolveEnv(array $param): array
{
    $param_keys = array_keys($param);

    for ($i = 0, $iMax = count($param); $i < $iMax; $i++) {
//            echo 'param value of ' . $param_keys[$i] . '\n';
//            print_r($param[$param_keys[$i]]);

        if (is_array($param[$param_keys[$i]])) {
            $param[$param_keys[$i]] = resolveEnv($param[$param_keys[$i]]);
        } elseif (is_string($param[$param_keys[$i]]) && str_starts_with($param[$param_keys[$i]], '%env(')) {
//                echo 'getting env variable \n';
            $actualValue = str_replace(array('%env(', ')%'), '', $param[$param_keys[$i]]);
//                print_r($actualValue);

            $param[$param_keys[$i]] = getenv($actualValue);
        }
    }

    return $param;
}

