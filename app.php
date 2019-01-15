<?php

require 'vendor/autoload.php';
use ExinOne\MixinSDK\MixinSDK;

$mixinSdk = new MixinSDK(require './config.php');

// $BotInfo = $mixinSdk->user()->readProfile();
// print_r($BotInfo);
$ids = [
    '0b4f49dc-8fb4-4539-9a89-fb3afc613747',
];
$a = $mixinSdk->message()->sendBatchMessage($ids,'Hello,Websocket!!!!');
print_r($a);
//-------
// Or more simple way, using the 'use' method , chained with other methods
// $mixinSdk->use('myConfig-A',$config)->user()->readProfile();
// then you can
// $mixinSdk->use('myConfig-A')->user()->readProfile();
