<?php

$router = new Phalcon\Mvc\Router(false);

//$router->setDefaultModule("frontend");
#$router->setDefaultNamespace("Eva\Frontend\Controllers");


$router->add('/admin/user', array(
    'module' => 'evauser',
    'controller' => 'Admin\User',
    'action' => 'index'
));

$router->add('/user', array(
    'module' => 'evauser',
    'controller' => 'user',
    'action' => 'index'
));

$router->add('/', array(
    'module' => 'frontend',
    'controller' => 'index',
    'action' => 'index'
));

$router->add('/post', array(
    'module' => 'evapost',
    'controller' => 'user',
    'action' => 'index'
));
return $router;
