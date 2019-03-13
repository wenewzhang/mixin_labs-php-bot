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
print_r($uuid . PHP_EOL);

$client = new GuzzleHttp\Client();
$res = $client->request('GET', 'https://exinone.com/exincore/markets?base_asset=815b0b1a-2764-3736-8faa-42d694fa620a', [
    ]);
echo $res->getStatusCode() . PHP_EOL;
$resInfo = json_decode($res->getBody(), true);
// print_r($resInfo);
// print_r($resInfo["data"]);
echo "------Asset ID | Asset Symbol | Price | Amount | Exchanges --------" . PHP_EOL;
foreach ($resInfo["data"] as $key => $coinInfo) {
  echo ($coinInfo["exchange_asset"] ." ".$coinInfo["exchange_asset_symbol"]. "/". $coinInfo["base_asset_symbol"] .
        " ". $coinInfo["price"] ." ". $coinInfo["minimum_amount"] ."-". $coinInfo["maximum_amount"] . " ");
  foreach ($coinInfo["exchanges"] as $key => $exchange) {
    echo $exchange . " ";
  }
  echo PHP_EOL;
}
// print_r($resInfo["data"]);
// foreach ($resInfo as $coinInfo) {
//     echo "e" . PHP_EOL;
//     print_r($coinInfo);
//     // echo ($coinInfo["exchange_asset"] . $coinInfo["exchange_asset_symbol"] . PHP_EOL);
// }
