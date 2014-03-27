#!/usr/bin/env php
<?php
require __DIR__ . '/../init_autoloader.php';

$worker = new GearmanWorker();
$worker->addServer();
$worker->addFunction('sendmailAsync', 'sendmailAsync');

$engine = new \Eva\EvaEngine\Engine(__DIR__ . '/..');

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
$logger = new Phalcon\Logger\Adapter\File($engine->getDI()->get('config')->logger->path . 'worker_sendmail_' .  date('Y-m-d') . '.log');

function sendmailAsync($job)
{
    global $engine;
    global $logger;
    $jobString = $job->workload();
    $logger->info(sprintf("Start sending mail by %s", $jobString));
    echo sprintf("Start sending mail by %s \n", $jobString);
    try {
        $work = json_decode($jobString, true);
        if($work) {
            $class = new $work['class'];
            call_user_func_array(array($class, $work['method']), $work['parameters']);
            $logger->info(sprintf("Mail sent to %s", $jobString));
            echo sprintf("Mail sent by %s \n", $jobString);
        } else {
            $logger->error(sprintf("Mail sent parameters error by %s \n", $jobString));
            echo sprintf("Mail sent error %s \n", $jobString);
        }
    } catch(\Exception $e) {
        echo sprintf("Exception catched %s, see log for details \n", $jobString);
        $logger->debug($e);
    }
}

while ($worker->work());
