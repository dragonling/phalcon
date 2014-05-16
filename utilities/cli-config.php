<?php
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Console\ConsoleRunner;

require __DIR__ . '/../init_autoloader.php';
$engine = new \Eva\EvaEngine\Engine(__DIR__ . '/..');
$engine->loadModules(array(
    'EvaCore',
));
$engine->bootstrap();
$config = $engine->getDI()->get('config');

$paths = array(__DIR__);
$isDevMode = true;

// the connection configuration
$dbParams = array(
    'driver'   => 'pdo_mysql',
    'user'     => $config->dbAdapter->master->username,
    'password' => $config->dbAdapter->master->password,
    'dbname'   => $config->dbAdapter->master->database,
);

$config = Setup::createAnnotationMetadataConfiguration($paths, $isDevMode);
$entityManager = EntityManager::create($dbParams, $config);

/** @var $em \Doctrine\ORM\EntityManager */
$platform = $entityManager->getConnection()->getDatabasePlatform();
$platform->registerDoctrineTypeMapping('enum', 'string');

return ConsoleRunner::createHelperSet($entityManager);
