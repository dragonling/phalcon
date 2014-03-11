<?php

/**
 * Services are globally registered in this file
 */

use Phalcon\Mvc\Router;
use Phalcon\Mvc\Url as UrlResolver;
use Phalcon\DI\FactoryDefault;
use Phalcon\Session\Adapter\Files as SessionAdapter;
use Phalcon\Db\Adapter\Pdo\Mysql as DbAdapter;
use Phalcon\Config;

/**
 * The FactoryDefault Dependency Injector automatically register the right services providing a full stack framework
 */
$di = new FactoryDefault();

$di['config'] = function () {
    $config = new Config(include __DIR__ . "/config.default.php");
    if(false === file_exists(__DIR__ . "/config.local.php")) {
        return $config;
    }
    return $config->merge(new Config(include __DIR__ . "/config.local.php"));
};
/**
 * Registering a router
 */
$di['router'] = function () {
	return include __DIR__ . "/routes.php";
};

/**
 * The URL component is used to generate all kind of urls in the application
 */
$di['url'] = function () {
    $url = new UrlResolver();
    $url->setBaseUri('/');

    return $url;
};

/**
 * Start the session the first time some component request the session service
 */
$di['session'] = function () {
    $session = new SessionAdapter();
    $session->start();

    return $session;
};


$di['db'] = function () use ($di) {
    $config = $di->get('config');
    return new DbAdapter(array(
        'host' => $config->dbAdapter->master->host,
        'username' => $config->dbAdapter->master->username,
        'password' => $config->dbAdapter->master->password,
        'dbname' => $config->dbAdapter->master->database,
    ));
};

$di->set('dbMaster', function () use ($di) {
    $config = $di->get('config');
    return new DbAdapter(array(
        'host' => $config->dbAdapter->master->host,
        'username' => $config->dbAdapter->master->username,
        'password' => $config->dbAdapter->master->password,
        'dbname' => $config->dbAdapter->master->database,
    ));
});


$di->set('dbSlave', function () use ($di) {
    $config = $di->get('config');
    $slaves = $config->dbAdapter->slave;
    $slaveKey = array_rand($slaves->toArray());
    return new DbAdapter(array(
        'host' => $config->dbAdapter->slave->$slaveKey->host,
        'username' => $config->dbAdapter->slave->$slaveKey->username,
        'password' => $config->dbAdapter->slave->$slaveKey->password,
        'dbname' => $config->dbAdapter->slave->$slaveKey->database,
    ));
});

