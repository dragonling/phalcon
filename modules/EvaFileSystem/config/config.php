<?php

return array(
    'filesystem' => array(
        'adapter' => 'local',
        'urlBaseForCDN' => '',  //Full http link
        'urlBaseForLocal' => '',  //Path is better
        'uploadTmpPath' => __DIR__ . '/../uploads/',
        'uploadPath' => __DIR__ . '/../uploads/',
        'uploadPathlevel' => 3,
        'localBackup' => false,
        'validator' => array(
            'maxFileSize' => '1M',
        )
    ),
);
