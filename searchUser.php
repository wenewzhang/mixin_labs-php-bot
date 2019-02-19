<?php
require __DIR__ . '/vendor/autoload.php';
use ExinOne\MixinSDK\MixinSDK;
$mixinSdk = new MixinSDK(require './config.php');
$userInfo = $mixinSdk->Network()->readUser("37222956");
$userInfo["user_id"];
print_r($userInfo);



$asset_infoNew = $mixinSdk->Wallet()->readAsset("6cfe566e-4aad-470b-8c9a-2fd35b49c68d");

print_r($asset_infoNew);
