<?php

return array(
    'filesystem' => array(
        'adapter' => 'local',
        'staticUri' => '',
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
