#!/usr/bin/env php
<?php
require __DIR__ . '/../init_autoloader.php';

$worker = new GearmanWorker();
$worker->addServer();
$worker->addFunction('sendmailAsync', 'sendmailAsync');

$engine = new Engine(__DIR__ . '/..');

$engine->loadModules(array(
    'EvaCore',
    'EvaUser',
    'EvaOAuthClient',
    'EvaOAuthServer',
    'EvaPost',
    'Frontend' => array(
        'className' => 'Eva\Frontend\Module',
        'path' => __DIR__ . '/../apps/Frontend/Module.php'
    ),
    'WscnGold' => array(
        'className' => 'WscnGold\Module',
        'path' => __DIR__ . '/../apps/WscnGold/Module.php'
    ),
));
$engine->bootstrap();

function sendmailAsync($job)
{
    global $engine;
    $jobString = $job->workload();
    $work = json_decode($jobString);
}

while ($worker->work());
