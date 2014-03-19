<?php
require __DIR__ . '/../init_autoloader.php';

use Eva\EvaEngine\Engine;

$debug = new \Phalcon\Debug();
$debug->listen();

$engine = new Engine(__DIR__ . '/..');
$engine->loadModules(array(
    'EvaCore',
    'EvaUser',
    'EvaOAuthClient',
    'EvaOAuthServer',
    'EvaPost',
    'Frontend' => array(
        'className' => 'Eva\Frontend\Module',
        'path' => __DIR__ . '/../apps/frontend/Module.php'
    ),
));
$engine->bootstrap()->run();




/*
try {

    require __DIR__ . '/../config/services.php';
    $application = new Application();
    $application->setDI($di);
    require __DIR__ . '/../config/modules.php';
    echo $application->handle()->getContent();

} catch (Exception $e) {
    echo '<pre>';
    echo get_class($e), ": ", $e->getMessage(), "\n";
    echo " File=", $e->getFile(), "\n";
    echo " Line=", $e->getLine(), "\n";
    echo $e->getTraceAsString();
    echo '</pre>';

    //echo $e->getMessage();
} catch (PDOException $e) {
    echo '<pre>';
    var_dump($e);
    echo '</pre>';
    //echo $e->getMessage();
}
*/
