<?php

require './../vendor/autoload.php';

$pms = \PMS\Client::create('ws://localhost:8080/ws');

//$pms->set('name_user_1', 'Alina');

$response = $pms->get('name_user_1');

var_dump($response);