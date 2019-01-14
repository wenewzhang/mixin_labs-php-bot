<?php
require 'vendor/autoload.php';
// use Wrench/Client;
use Wrench\Client;

function onData($Arg) {
  print("on_data_callback\n");
  print($Arg);
  // $client->sendData('hello,world!');
}

$client = new Client('ws://localhost:8080/echo',
                      'ws://localhost:8080/echo',
                      [
            'on_data_callback' => onData]
                    );
// $client = new Client('wss://blaze.mixin.one/', 'https://google.com');
$client->connect();
$client->sendData('hello,world!');
$response = $client->receive()[0]->getPayload();

while (true) {
  // print('in while');
  sleep(1);
}
$client->disconnect();
