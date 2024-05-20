<?php

require './../vendor/autoload.php';

$pms = \PMS\Client::create('ws://localhost:8080/ws');

$time_start = microtime(true);

for ($i = 0; $i < 100000; $i++) {
    $pms->set('test_key' . $i, 'test_value' . $i);
    $response = $pms->get('test_key' . $i);
    if (!$response->isError()) {
        //echo $response->getValue() . PHP_EOL;
    } else {
        echo 'error:' . $response->getError() . PHP_EOL;
    }
}

$time_end = microtime(true);
$execution_time = $time_end - $time_start;
echo $execution_time . PHP_EOL;