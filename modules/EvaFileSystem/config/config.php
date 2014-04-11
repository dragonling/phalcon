<?php

return array(
    'routes' =>  array(
        '/admin/upload' =>  array(
            'module' => 'EvaFileSystem',
            'controller' => 'Admin\Upload',
        ),
        '/admin/upload/:action' =>  array(
            'module' => 'EvaFileSystem',
            'controller' => 'Admin\Upload',
            'action' => 1,
        ),
    ),

    'filesystem' => array(
        'adapter' => 'local',
        'uploadTmpPath' => __DIR__ . '/../uploads/',
        'uploadPath' => __DIR__ . '/../uploads/',
        'uploadPathlevel' => 3,
        'uploadUrlBase' => '',
        'localBackup' => false,
        'validator' => array(
            'maxFileSize' => '1M',
        )
    ),
);
