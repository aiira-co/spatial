<?php
// cli-config.php
require_once '.' . DS . 'config' . DS . 'config.php';
$config = (new Config)->cliConfig;

$filePath = '.' . DS . 'src' . DS . 'Infrastructure' . DS . $config['namespace'] . DS . $config['class'] . '.php';
if (file_exists($filePath)) {
    include_once $filePath;

    if (class_exists('\\Infrastructure\\' . ucfirst($config['namespace']) . '\\' . $config['class'])) {
        $em = 'em' . ucfirst(str_replace('DB', '', $config['class']));
        $class = '\\Infrastructure\\' . ucfirst($config['namespace']) . '\\' . $config['class'];

        return \Doctrine\ORM\Tools\Console\ConsoleRunner::createHelperSet((new $class)->$em);
    } else {
        die('Class ' . '\\Infrastructure\\' . ucfirst($config['namespace']) . '\\' . $config['class'] . ' not found!');
    }
} else {
    die('file not found --> ' . $filePath);
}
