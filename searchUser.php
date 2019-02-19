<?php
require __DIR__ . '/vendor/autoload.php';
use ExinOne\MixinSDK\MixinSDK;
$mixinSdk = new MixinSDK(require './config.php');
$userInfo = $mixinSdk->Network()->readUser("37222956");
$userInfo["user_id"];
print_r($userInfo);
