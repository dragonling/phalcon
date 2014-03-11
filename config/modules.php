<?php

/**
 * Register application modules
 */
$application->registerModules(array(
    'frontend' => array(
        'className' => 'Eva\Frontend\Module',
        'path' => __DIR__ . '/../apps/frontend/Module.php'
    ),
    'evauser' => array(
        'className' => 'Eva\EvaUser\Module',
        'path' => __DIR__ . '/../modules/EvaUser/Module.php'
    ),
    'evapost' => array(
        'className' => 'Eva\EvaPost\Module',
        'path' => __DIR__ . '/../modules/EvaPost/Module.php'
    ),
));
