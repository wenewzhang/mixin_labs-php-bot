<?php

require 'vendor/autoload.php';
use ExinOne\MixinSDK\MixinSDK;

$mixinSdk = new MixinSDK(require './config.php');

$BotInfo = $mixinSdk->user()->readProfile();
print_r($BotInfo);
//-------
// Or more simple way, using the 'use' method , chained with other methods
// $mixinSdk->use('myConfig-A',$config)->user()->readProfile();
// then you can
// $mixinSdk->use('myConfig-A')->user()->readProfile();
