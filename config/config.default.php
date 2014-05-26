<?php

return array(
    'debug' => 1,
    'error' => array(
        'disableLog' => 0,
        'logPath' => __DIR__ . '/../logs/',
        'controllerNamespace' => '',
        'controller' => 'error',
        'action' => 'index',
        'viewpath' => '',

    ),

    'cache' => array(
        'enable' => false,
        'viewCache' => array(
            'enable' => true,
            'frontend' => array(
                'adapter' => 'Output',
                'options' => array(),
            ),
            'backend' => array(
                'adapter' => 'File',
                'options' => array(
                    'cacheDir' => __DIR__ . '/../cache/view/',
                ),
            ),
        ),
    ),

    'baseUri' => '/',

    'thumbnail' => array(
        'default' => array(
            'enable' => false,
            'baseUri' => '',
            'errorUri' => '',
        ),
        'thumbers' => array(
            'uploads' => array(
                'adapter' => 'gd',
                'cache' => 1,
                'source_path' => __DIR__ . '/../public/uploads',
                'thumb_cache_path' => __DIR__ . '/../public/thumbnails/thumb',
            ),
        ),
    ),

    'app' => array(
        'title' => 'EvaEngine',
        'subtitle' => '',
    ),

    'logger' => array(
        'adapter' => 'File',
        'path' => __DIR__ . '/../logs/',
//        'defaultName' => 'system',
    ),

    'translate' => array(
        'enable' => true,
        'path' => __DIR__ . '/../languages/',
        'adapter' => 'csv',
        'forceLang' => 'zh_CN',
    ),

    'routes' => array(

    ),

    'datetime' => array(
        'defaultTimezone' => 8,
        //'defaultFormat' => 'F j, Y, g:i a',
        'defaultFormat' => 'Y年m月d日 H:i:s',
    ),

    'filesystem' => array(
        'adapter' => 'local',
        'urlBaseForLocal' => '/uploads',
        //'urlBaseForCDN' => '',
        'uploadPath' => __DIR__ . '/../public/uploads/',
        'uploadTmpPath' => __DIR__ . '/../public/tmp/',
    ),

    'dbAdapter' => array(
        'master' => array(
            'driver' => 'Pdo_Mysql',
            'host' => '192.168.1.228',
            'database' => 'eva',
            'username' => 'root',
            'password' => '',
            'charset'  => 'utf8'
        ),
        'slave' => array(
            'slave1' => array(
                'driver' => 'Pdo_Mysql',
                'host' => '192.168.1.233',
                'database' => 'eva',
                'username' => 'root',
                'password' => '',
                'charset'  => 'utf8'
            ),
        )
    ),

    'modelsMetadata' => array(
        'enable' => true,
        'adapter' => 'File',
        'options' => array(
            'metaDataDir' => __DIR__ . '/../cache/schema/'

        ),
    ),

    'queue' => array(
        'servers' => array(
            'server1' => array(
                'host' => '127.0.0.1',
                'port' => 4730,
            ),
        ),
    ),

    'mailer' => array(
        'async' => false,
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
