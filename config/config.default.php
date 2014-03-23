<?php

return array(
    'debug' => 0,
    'baseUri' => '/',

    'app' => array(
        'title' => 'EvaEngine',
        'subtitle' => '',
    ),

    'logger' => array(
        'adapter' => 'File',
        'path' => __DIR__ . '/../logs/',
//        'defaultName' => 'system',
    ), 

    'routes' => array(
        '/' =>  array(
            'module' => 'Frontend',
            'controller' => 'index',
            'action' => 'index'
        ),
    ),

    'dbAdapter' => array(
        'master' => array(
            'driver' => 'Pdo_Mysql',
            'host' => '192.168.1.228',
            'database' => 'eva',
            'username' => 'root',
            'password' => '',
        ),
        'slave' => array(
            'slave1' => array(
                'driver' => 'Pdo_Mysql',
                'host' => '192.168.1.233',
                'database' => 'eva',
                'username' => 'root',
                'password' => '',
            ),
        )
    ),


    'mailer' => array(
        'transport' => 'smtp', //or default
        'host' => 'smtp.gmail.com',
        'port' => 465,
        'encryption' => 'ssl',
        'username' => 'username',
        'password' => 'password',
        'defaultFrom' => array('noreply@wallstreetcn.com' => 'WallsteetCN'),
        'systemPath' => 'http://evaengine.com/',
        'staticPath' => 'http://evaengine.com/',
    ),

);
