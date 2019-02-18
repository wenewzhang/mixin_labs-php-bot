<?php
require __DIR__ . '/vendor/autoload.php';
use ExinOne\MixinSDK\MixinSDK;
$mixinSdk = new MixinSDK(require './config.php');
// print_r();
$info = $mixinSdk->network()->createUser("Tom cat");
print_r($info);
