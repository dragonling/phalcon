<?php

$router = new Phalcon\Mvc\Router();

//$router->setDefaultModule("Frontend");
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

$router->add('/user/:action', array(
    'module' => 'EvaUser',
    'controller' => 'user',
    'action' => 1,
));


$router->add('/auth/:action/(\w+)/(oauth1|oauth2)*', array(
    'module' => 'EvaOAuthClient',
    'controller' => 'auth',
    'action' => 1,
    'service' => 2,
    'auth' => 3,
));


$router->add('/oauth/authorize', array(
    'module' => 'EvaOAuthServer',
    'controller' => 'auth',
    'action' => 'authorize',
));

$router->add('/oauth/token', array(
    'module' => 'EvaOAuthServer',
    'controller' => 'auth',
    'action' => 'token',
));
return $router;
