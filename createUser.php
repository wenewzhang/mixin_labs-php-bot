<?php
require __DIR__ . '/vendor/autoload.php';
use ExinOne\MixinSDK\MixinSDK;
$mixinSdk = new MixinSDK(require './config.php');

const PIN       = "945689";
const MASTER_ID = "37222956";
const ASSET_ID  = "c6d0c728-2624-429b-8e0d-d9d19b6592fa";
// const ASSET_ID  = "965e5c6e-434c-3fa9-b780-c50f43cd955c";
const AMOUNT    = "0.001";

$user_info = $mixinSdk->Network()->createUser("Tom cat");
print_r($user_info);
print($user_info["pubKey"]);

$newConfig = array();
$newConfig["private_key"] = $user_info["priKey"];
$newConfig["pin_token"]   = $user_info["pin_token"];
$newConfig["session_id"]  = $user_info["session_id"];
$newConfig["client_id"]   = $user_info["user_id"];
$newConfig["pin"]         = PIN;
$mixinSdkNew = new MixinSDK($newConfig);

$pinInfo = $mixinSdkNew->Pin()->updatePin('',PIN);
print_r($pinInfo);

//
// $pubKey1 = str_replace("-----BEGIN PUBLIC KEY-----", '', $user_info["pubKey"]) ;
// $pubKey = trim(str_replace("-----END PUBLIC KEY-----", '', $pubKey1));
// echo "public key:".$pubKey;
// $btc_addr = $mixinSdk->Wallet()->createAddress("965e5c6e-434c-3fa9-b780-c50f43cd955c",
//                                                 "0xb8d5A3F0e2118B1DcE5c17015F2435D9d6e76668",
//                                                 $mixinSdk->getConfig('default')['pin'],
//                                                 "BTC",false);
// $asset_info = $mixinSdk->Wallet()->readAsset("c6d0c728-2624-429b-8e0d-d9d19b6592fa");
// print_r($asset_info);
// $asset_infoNew = $mixinSdkNew->Wallet()->readAsset("c6d0c728-2624-429b-8e0d-d9d19b6592fa");
// print_r($asset_infoNew);

$trans_info = $mixinSdk->Wallet()->transfer(ASSET_ID,$newConfig["client_id"],
                                         $mixinSdk->getConfig()['default']['pin'],AMOUNT);
print_r($trans_info);
$asset_infoNew = $mixinSdkNew->Wallet()->readAsset(ASSET_ID);
print_r($asset_infoNew);

$userInfo = $mixinSdk->Network()->readUser(MASTER_ID);
$userInfo["user_id"];

$trans_info2 = $mixinSdkNew->Wallet()->transfer(ASSET_ID,$userInfo["user_id"],
                                         $mixinSdkNew->getConfig()['default']['pin'],AMOUNT);

print_r($trans_info2);
// $asset_infosNew = $mixinSdk->Wallet()->readAssets();
// print_r($asset_infosNew);
// foreach($asset_infosNew as $x => $x_value) {
//     echo $x_value["symbol"]."|".$x_value["asset_id"]."\n";
//     // echo "<br>";
// }
// $asset_info = $mixinSdk->Wallet()->readAsset("965e5c6e-434c-3fa9-b780-c50f43cd955c");
// print_r($asset_info);
// $asset_info = $mixinSdk->Wallet()->readAsset("6cfe566e-4aad-470b-8c9a-2fd35b49c68d");
// print_r($asset_info);
