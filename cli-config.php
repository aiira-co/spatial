<?php
// cli-config.php
$dbClass = 'AppDB';
$space = 'resource';
$filePath = './src/Infrastructure/' . $space . '/' . $dbClass . '.php';
if (file_exists($filePath)) {
    require_once $filePath;

    if (class_exists('\\Infrastructure\\' . ucfirst($space) . '\\' . $dbClass)) {
        $em = 'em' . str_replace('DB', '', $dbClass);
        $class = '\\Infrastructure\\' . ucfirst($space) . '\\' . $dbClass;

        return \Doctrine\ORM\Tools\Console\ConsoleRunner::createHelperSet((new $class)->$em);
    } else {
        die('Class ' . '\\Infrastructure\\' . ucfirst($space) . '\\' . $dbClass . ' not found!');
    }
} else {
    die('file not found');
}
