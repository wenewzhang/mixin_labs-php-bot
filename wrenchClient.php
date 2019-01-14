<?php
require 'vendor/autoload.php';
// use Wrench/Client;
use Wrench\Client;
$client = new Client('ws://wenewzha:8080/echo','ws://wenewzha:8080/');
// $client = new Client('wss://blaze.mixin.one/', 'https://google.com');
$client->connect();
$client->sendData('hello');
$response = $client->receive()[0]->getPayload();
$client->disconnect();
