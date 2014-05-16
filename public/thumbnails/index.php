<?php
/**
 * EvaThumber
 * URL based image transformation php library
 *
 * @link      https://github.com/AlloVince/EvaThumber
 * @copyright Copyright (c) 2012-2013 AlloVince (http://avnpc.com/)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @author    AlloVince
 */

error_reporting(E_ALL);

// Check php version
if ( version_compare(phpversion(), '5.3.0', '<') ) {
    die(printf('PHP 5.3.0 is required, you have %s', phpversion()));
}

require __DIR__ . '/../../init_autoloader.php';

$engine = new \Eva\EvaEngine\Engine(__DIR__ . '/../../');
$engine->bootstrap();
$localConfig = $engine->getDI()->get('config');

$config = new EvaThumber\Config\Config(include __DIR__ . '/config.default.php');
if (isset($localConfig->thumbnail->thumbers)) {
    $config = $config->merge(new EvaThumber\Config\Config(array(
        'thumbers' => $localConfig->thumbnail->thumbers->toArray())
    ));
}

try {
    $thumber = new EvaThumber\Thumber($config);
    $thumber->show();
} catch (Exception $e) {
    if (isset($localConfig->thumbnail->default->errorUri) && $url = $localConfig->thumbnail->default->errorUri) {
        header("location:$url?msg=" . urlencode($e->getMessage()));
    } else {
        throw $e;
    }
}
