<?php

return array(
    '/admin/media' =>  array(
        'module' => 'EvaFileSystem',
        'controller' => 'Admin\Media',
    ),
    '/admin/media/:action(/(\d+))*' =>  array(
        'module' => 'EvaFileSystem',
        'controller' => 'Admin\Media',
        'action' => 1,
        'id' => 3,
    ),
    '/admin/upload' =>  array(
        'module' => 'EvaFileSystem',
        'controller' => 'Admin\Upload',
    ),
    '/admin/upload/:action' =>  array(
        'module' => 'EvaFileSystem',
        'controller' => 'Admin\Upload',
        'action' => 1,
    ),
);
