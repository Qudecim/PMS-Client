<?php

require './../vendor/autoload.php';

$pms = \PMS\Client::create('ws://localhost:8080/ws');

$time_start = microtime(true);

$pms->set('test_key1', 'test_value1');
$pms->set('test_key2', 'test_value2');
$response = $pms->get('test_key1');
echo $response->getValue() . PHP_EOL;


$pms->push('list_key1', 'test_key1');
$pms->push('list_key1', 'test_key2');
$response = $pms->pull('list_key1');
echo implode(',', $response->getItems()) . PHP_EOL;


$pms->set('test_key1', 'test_value3');
$response = $pms->get('test_key1');
echo $response->getValue() . PHP_EOL;


$response = $pms->pull('list_key1');
echo implode(',', $response->getItems()) . PHP_EOL;

$time_end = microtime(true);
$execution_time = $time_end - $time_start;
echo $execution_time . PHP_EOL;