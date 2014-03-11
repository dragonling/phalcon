<?php

return array(
    'baseUri' => '/',
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
);
