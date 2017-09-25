<?php


$base = ['path'=>'/', 'controller'=>'app', 'title'=>'Welcome Home'];
$values = ['path'=>'values', 'controller'=>'values'];



 $router = CORE::getInstance('Router');

 $router->setRouter(
                    $base,
                    $values
                  );



?>
