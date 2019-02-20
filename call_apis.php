<?php
require __DIR__ . '/vendor/autoload.php';
use ExinOne\MixinSDK\MixinSDK;
$mixinSdk = new MixinSDK(require './config.php');

const PIN             = "945689";
const MASTER_ID       = "37222956";
const BTC_ASSET_ID    = "c6d0c728-2624-429b-8e0d-d9d19b6592fa";
const EOS_ASSET_ID    = "6cfe566e-4aad-470b-8c9a-2fd35b49c68d";
const BTC_WALLET_ADDR = "14T129GTbXXPGXXvZzVaNLRFPeHXD1C25C";
const AMOUNT          = "0.001";
// Mixin Network support cryptocurrencies (2019-02-19)
// |EOS|6cfe566e-4aad-470b-8c9a-2fd35b49c68d
// |CNB|965e5c6e-434c-3fa9-b780-c50f43cd955c
// |BTC|c6d0c728-2624-429b-8e0d-d9d19b6592fa
// |ETC|2204c1ee-0ea2-4add-bb9a-b3719cfff93a
// |XRP|23dfb5a5-5d7b-48b6-905f-3970e3176e27
// |XEM|27921032-f73e-434e-955f-43d55672ee31
// |ETH|43d61dcd-e413-450d-80b8-101d5e903357
// |DASH|6472e7e3-75fd-48b6-b1dc-28d294ee1476
// |DOGE|6770a1e5-6086-44d5-b60f-545f9d9e8ffd
// |LTC|76c802a2-7c88-447f-a93e-c29c9e5dd9c8
// |SC|990c4c29-57e9-48f6-9819-7d986ea44985
// |ZEN|a2c5d22b-62a2-4c13-b3f0-013290dbac60
// |ZEC|c996abc9-d94e-4494-b1cf-2a3fd3ac5714
// |BCH|fd11b6e3-0b87-41f1-a41f-f0e9b49e5bf0

$msg  = "1: Create user and update PIN\n2: Read Bitcoin balance \n3: Read Bitcoin Address\n4: Read EOS balance\n";
$msg .= "5: Read EOS address\n6: Transfer Bitcoin from bot to new user\n7: Transfer Bitcoin from new user to Master\n";
$msg .= "8: withdraw bot's Bitcoin\n";
$msg .= "9: exit \nMake your choose:";
while (true) {
  $line = readline($msg);
  if ($line != '9') print("run...\n");
  if ($line == '1') {
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
    $csvary = array($newConfig);
    $fp = fopen('new_users.csv', 'a');
    foreach ($csvary as $fields) {
        fputcsv($fp, $fields);
    }
    fclose($fp);
  }
  if ($line == '2') {
    if (($handle = fopen("new_users.csv", "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
      $mixinSdkNew = new MixinSDK(GenerateConfigByCSV($data));
      $asset_info = $mixinSdkNew->Wallet()->readAsset(BTC_ASSET_ID);
      print_r("Bitcoin wallet balance is :".$asset_info["balance"]."\n");
    }
      fclose($handle);
    } else print("Create user first\n");
  }
  if ($line == '3') {
    if (($handle = fopen("new_users.csv", "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
      $mixinSdkNew = new MixinSDK(GenerateConfigByCSV($data));
      $asset_info = $mixinSdkNew->Wallet()->readAsset(BTC_ASSET_ID);
      print_r("Bitcoin wallet address is :".$asset_info["public_key"]."\n");
    }
      fclose($handle);
    } else print("Create user first\n");
  }
  if ($line == '4') {
    if (($handle = fopen("new_users.csv", "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
      $mixinSdkNew = new MixinSDK(GenerateConfigByCSV($data));
      $asset_info = $mixinSdkNew->Wallet()->readAsset(EOS_ASSET_ID);
      print_r("EOS wallet balance is :".$asset_info["balance"]."\n");
    }
      fclose($handle);
    } else print("Create user first\n");
  }
  if ($line == '5') {
    if (($handle = fopen("new_users.csv", "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
      $mixinSdkNew = new MixinSDK(GenerateConfigByCSV($data));
      $asset_info = $mixinSdkNew->Wallet()->readAsset(EOS_ASSET_ID);
      print_r("EOS wallet address is :".$asset_info["account_name"]."\n");
      print_r($asset_info["account_tag"]."\n");
    }
      fclose($handle);
    } else print("Create user first\n");

  }
  if ($line == '6') {
    if (($handle = fopen("new_users.csv", "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
      // $mixinSdkNew = new MixinSDK(GenerateConfigByCSV($data));
      $trans_info = $mixinSdk->Wallet()->transfer(BTC_ASSET_ID,$data[3],
                                               $mixinSdk->getConfig()['default']['pin'],AMOUNT);
      print_r($trans_info);
    }
      fclose($handle);
    } else print("Create user first\n");
  }
  if ($line == '7') {
    $userInfo = $mixinSdk->Network()->readUser(MASTER_ID);
    if (isset($userInfo["user_id"])) {
      if (($handle = fopen("new_users.csv", "r")) !== FALSE) {
      while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
          $mixinSdkNew = new MixinSDK(GenerateConfigByCSV($data));
          $asset_info = $mixinSdkNew->Wallet()->readAsset(BTC_ASSET_ID);
          if ( (float) $asset_info["balance"] > 0 ) {
            $trans_info = $mixinSdkNew->Wallet()->transfer(BTC_ASSET_ID,$userInfo["user_id"],
                                                     $mixinSdkNew->getConfig()['default']['pin'],$asset_info["balance"]);
            print_r($trans_info);
          } else print($data[3] . " has no coins!\n");
      }
        fclose($handle);
      } else print("Create user first\n");
    } else print("Can not find this user id by Mixin ID!");
  }
  if ($line == '8') {
    $btc = $mixinSdk->Wallet()->createAddress(BTC_ASSET_ID,
                                              BTC_WALLET_ADDR,
                                              $mixinSdk->getConfig()['default']['pin'],
                                              "BTC withdral",false);
    print("Bitcoin winthdrawal fee is:".$btc["fee"]."\n");
    $wdInfo = $mixinSdk->Wallet()->withdrawal($btc["address_id"],
                                AMOUNT,
                                $mixinSdk->getConfig()['default']['pin'],
                                "BTC withdral");
  }
  if ($line == '9') {
    exit();
  }
}

function GenerateConfigByCSV($data) :array {
  print("client id is:".$data[3]."\n");
  $newConfig = array();
  $newConfig["private_key"] = $data[0];
  $newConfig["pin_token"]   = $data[1];
  $newConfig["session_id"]  = $data[2];
  $newConfig["client_id"]   = $data[3];
  $newConfig["pin"]         = $data[4];
  return $newConfig;
}
