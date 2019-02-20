<?php
require __DIR__ . '/vendor/autoload.php';
use ExinOne\MixinSDK\MixinSDK;
$mixinSdk = new MixinSDK(require './config.php');

const PIN           = "945689";
const MASTER_ID     = "37222956";//Developer Mixin messenger account ID
const BTC_ASSET_ID  = "c6d0c728-2624-429b-8e0d-d9d19b6592fa";
// const ASSET_ID   = "965e5c6e-434c-3fa9-b780-c50f43cd955c";
const AMOUNT        = "0.001";

//Mixin PHP SDK generate RSA key pair automatically
$user_info = $mixinSdk->Network()->createUser("Tom cat");
//Create User api include all account information
print_r($user_info);
print($user_info["pubKey"]);

$newConfig = array();
$newConfig["private_key"] = $user_info["priKey"];
$newConfig["pin_token"]   = $user_info["pin_token"];
$newConfig["session_id"]  = $user_info["session_id"];
$newConfig["client_id"]   = $user_info["user_id"];
$newConfig["pin"]         = PIN;
//Install the parameter in PHP SDK
$mixinSdkNew = new MixinSDK($newConfig);

//Create a PIN.
$pinInfo = $mixinSdkNew->Pin()->updatePin('',PIN);
print_r($pinInfo);
print_r($trans_info);
//Create Bitcoin deposit address by read Bitcoin asset
$asset_infoNew = $mixinSdkNew->Wallet()->readAsset(BTC_ASSET_ID);
print_r("BitCoin wallet address is :".$asset_infoNew["public_key"]);

//Transfer Bitcoin to developer's account, zero fee and confirmed instantly
$trans_info = $mixinSdk->Wallet()->transfer(BTC_ASSET_ID,$newConfig["client_id"],
                                         $mixinSdk->getConfig()['default']['pin'],AMOUNT);
print_r($trans_info);

//Read user information by ID
$userInfo = $mixinSdk->Network()->readUser(MASTER_ID);
$userInfo["user_id"];

$trans_info2 = $mixinSdkNew->Wallet()->transfer(BTC_ASSET_ID,$userInfo["user_id"],
                                         $mixinSdkNew->getConfig()['default']['pin'],AMOUNT);

print_r($trans_info2);
