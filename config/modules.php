<?php

/**
 * Register application modules
 */
$application->registerModules(array(
    'frontend' => array(
        'className' => 'Eva\Frontend\Module',
        'path' => __DIR__ . '/../apps/frontend/Module.php'
    ),
    'user' => array(
        'className' => 'Eva\EvaUser\Module',
        'path' => __DIR__ . '/../modules/EvaUser/Module.php'
    )
));
