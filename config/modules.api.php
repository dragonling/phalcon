<?php
return array(
    'EvaCore' => array(
        'routesFrontend' => false,
        'routesBackend' => false,
    ),
    'EvaUser' => array(
        'routesFrontend' => false,
        'routesBackend' => false,
    ),
    'EvaBlog' => array(
        'routesFrontend' => false,
        'routesBackend' => false,
    ),
    'EvaFileSystem' => array(
        'routesFrontend' => false,
        'routesBackend' => false,
    ),
    'WscnApiVer2' => array(
        'className' => 'WscnApiVer2\Module',
        'path' => __DIR__ . '/../apps/WscnApiVer2/Module.php',
        'routesFrontend' => __DIR__ . '/../apps/WscnApiVer2/config/routes.frontend.php',
        'routesBackend' => __DIR__ . '/../apps/WscnApiVer2/config/routes.backend.php',
    ),
);
