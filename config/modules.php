<?php
return array(
    'EvaCore',
    'EvaUser',
    'EvaOAuthClient',
    'EvaOAuthServer',
    'EvaBlog',
    'EvaFileSystem',
    'EvaComment',
    'Frontend' => array(
        'className' => 'Eva\Frontend\Module',
        'path' => __DIR__ . '/../apps/Frontend/Module.php',
    ),
    'WscnGold' => array(
        'className' => 'WscnGold\Module',
        'path' => __DIR__ . '/../apps/WscnGold/Module.php'
    ),
);
