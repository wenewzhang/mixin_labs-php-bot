<?php
require 'vendor/autoload.php';

use Ramsey\Uuid\Uuid;
use MessagePack\MessagePack;
use GuzzleHttp\Client;

// Pack memo
$memo = base64_encode(MessagePack::pack([
                      'A' => Uuid::fromString('c6d0c728-2624-429b-8e0d-d9d19b6592fa')->getBytes(),
                      ]));
// gaFBxBDG0McoJiRCm44N2dGbZZL6

// Parse memo
$uuid = Uuid::fromBytes(
                        MessagePack::unpack(base64_decode($memo))['A']
                        )->toString();
print_r($memo."\n");
print_r($uuid);

$client = new GuzzleHttp\Client();
$res = $client->request('GET', 'https://exinone.com/exincore/markets?base_asset=815b0b1a-2764-3736-8faa-42d694fa620a', [
    ]);
echo $res->getStatusCode();
// "200"
echo $res->getHeader('content-type')[0];
// 'application/json; charset=utf8'
echo $res->getBody();
