<?php

$router = new Phalcon\Mvc\Router();

//$router->setDefaultModule("frontend");
#$router->setDefaultNamespace("Eva\Frontend\Controllers");

$router->add('/admin(/)*', array(
    'module' => 'EvaCore',
    'controller' => 'Admin\Index',
    'action' => 'index'
));

$router->add('/admin/user', array(
    'module' => 'EvaUser',
    'controller' => 'Admin\User',
    'action' => 'index'
));

$router->add('/user', array(
    'module' => 'EvaUser',
    'controller' => 'user',
    'action' => 'index'
));

$router->add('/', array(
    'module' => 'Frontend',
    'controller' => 'index',
    'action' => 'index'
));

$router->add('/post', array(
    'module' => 'EvaPost',
    'controller' => 'user',
    'action' => 'index'
));

$router->add('/post', array(
    'module' => 'EvaPost',
    'controller' => 'user',
    'action' => 'index'
));

$router->add('/auth/:action/(\w+)/(oauth1|oauth2)*', array(
    'module' => 'EvaOAuthClient',
    'controller' => 'auth',
    'action' => 1,
    'service' => 2,
    'auth' => 3,
));
return $router;
