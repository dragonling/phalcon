<?php

return array(
    'routes' =>  array(
        '/admin/upload' =>  array(
            'module' => 'EvaFileSystem',
            'controller' => 'upload',
        ),
        '/admin/upload/:action' =>  array(
            'module' => 'EvaFileSystem',
            'controller' => 'upload',
            'action' => 1,
        ),
    ),

    'upload' => array(
        'adapter' => 'local',
        'path' => __DIR__ . '/../uploads/',
        'pathlevel' => 3,
        'url' => '',
    ),
);
