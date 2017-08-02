<?php
require '../config.php';

if (!empty($config['debug'])) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
}

require '../vendor/autoload.php';
require 'lib/Runner.php';

$task = new Tasks\Runner($config);
$task->climate = new League\CLImate\CLImate;

$task->daemon('Daemon', [
    'sleep_time' => $config['sleep_time']
]);
