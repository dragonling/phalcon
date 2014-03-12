<?php

/**
 * Register application modules
 */
$application->registerModules(array(
    'frontend' => array(
        'className' => 'Eva\Frontend\Module',
        'path' => __DIR__ . '/../apps/frontend/Module.php'
    ),
    'evacore' => array(
        'className' => 'Eva\EvaCore\Module',
        'path' => __DIR__ . '/../modules/EvaCore/Module.php'
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


$modules = $application->getModules();
$loader = new \Phalcon\Loader();
$loaderArray = array();
foreach($modules as $module) {
    $loaderArray[$module['className']] = $module['path'];
}
$loader->registerClasses($loaderArray)->register();
$loaderArray = array();
foreach($modules as $module) {
    $moduleLoader = method_exists($module['className'], 'registerGlobalAutoloaders') ? 
        $module['className']::registerGlobalAutoloaders() :
        array();
    if($moduleLoader instanceof $loader) {
        continue;
    }
    $loaderArray += $moduleLoader;
}
if($loaderArray) {
    $loader->registerNamespaces($loaderArray)->register();
}

