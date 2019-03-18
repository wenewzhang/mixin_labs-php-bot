<?php
require __DIR__ . '/vendor/autoload.php';
use ExinOne\MixinSDK\MixinSDK;
$mixinSdk = new MixinSDK(require './config.php');
$userInfo = $mixinSdk->Network()->readUser("37222956");
$userInfo["user_id"];
print_r($userInfo);



$asset_infoNew = $mixinSdk->Wallet()->readAsset("c6d0c728-2624-429b-8e0d-d9d19b6592fa");

print_r($asset_infoNew);
