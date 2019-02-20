In last two chapters, we create a bot to [receive user's message and send message back to user](https://github.com/wenewzhang/mixin_labs-php-bot/blob/master/README.md), the bot can [receive Bitcoin and send it back to user](https://github.com/wenewzhang/mixin_labs-php-bot/blob/master/README2.md). Next, we will show you how to create a Bitcoin wallet, read and send Bitcoin by PHP based on Mixin Network API.

#  Create a Bitcoin wallet of Mixin Network by PHP
Creating a Bitcoin wallet is very easy on Mixin Network. Developer just need to prepare an unique RSA key pair and call Mixin Network API to create a Mixin Network account. The account not only support Bitcoin wallet, but also support Ethereum, EOS, Etc. Full blockchain support list is [here](https://mixin.one/network/chains). All ERC20 Token and EOS token are also supported by the account.

## Create a Mixin Network account to acquire Bitcoin wallet
You may ask where is Bitcoin private key? The private key is protected by multi signature from Mixin Network. Bitcoin Asset can only be withdraw to other address when user provide correct RSA private key signature, PIN code and Session key.
Mixin Network doesn't handle the RSA private key which is created by the mixin-sdk-php, so please keep in mind: **STORE YOUR PRIVATE KEY SAFETY!**
```
$user_info = $mixinSdk->Network()->createUser("Tom cat");
```
What's happened when issue createUser function? Which create a private key, public key and session secret.
In fact, session secret is a public key which is used to generate Bitcoin wallet address.
```php
...
$session_secret = str_replace(["-----BEGIN PUBLIC KEY-----\n", "-----END PUBLIC KEY-----", "\n"], '', $pub_key);
...

[$priKey, $pubKey, $session_secret] = $this->generateSSLKey();
$body = [
    'session_secret' => $session_secret,
    'full_name'      => (string) $fullName,
];
...
```
## Read Bitcoin asset balance and deposit address
The address of Bitcoin is not generated automatically at same time when the account is created, it is created when user read Bitcoin asset for the first time.
Now we can read the Bitcoin asset by Deposit/Read Asset API after we created an account.
```php
$asset_infoNew = $mixinSdkNew->Wallet()->readAsset(ASSET_ID);
echo "BitCoin wallet address is :".$asset_infoNew["public_key"];
```
You can found information about Bitcoin asset in the account. Public key is the deposit address for you.
```php
Array
(
    [type] => asset
    [asset_id] => c6d0c728-2624-429b-8e0d-d9d19b6592fa
    [chain_id] => c6d0c728-2624-429b-8e0d-d9d19b6592fa
    [symbol] => BTC
    [name] => Bitcoin
    [icon_url] => https://images.mixin.one/HvYGJsV5TGeZ-X9Ek3FEQohQZ3fE9LBEBGcOcn4c4BNHovP4fW4YB97Dg5LcXoQ1hUjMEgjbl1DPlKg1TW7kK6XP=s128
    [balance] => 0
    [public_key] => 195p8R8Y15uzDGMrdVkELVUW2444psqiSq
    [account_name] =>
    [account_tag] =>
    [price_btc] => 1
    [price_usd] => 3928.11498197
    [change_btc] => 0
    [change_usd] => -0.006841408545228452
    [asset_key] => c6d0c728-2624-429b-8e0d-d9d19b6592fa
    [confirmations] => 12
    [capitalization] => 0
)
```
By the way, EOS wallet is different:
```php
[account_name] => eoswithmixin
[account_tag] => aae7be03e8ac0d927dcf2fd5a0e5b65c
```

## Deposit some Bitcoin into the address from other exchange or wallet
Now you can deposit some bitcoin from other exchange or wallet. This is maybe too expensive for our tutorial. So you can use your own Mixin messenger user account to transfer super tiny bitcoin to the account you just created. It is free and confirmed instantly.

Transfer Bitcoin to the account is very easy. You just need to prepare a URL like:

```
```

Now you can read Bitcoin balance of the account again to confirm the action.
```php
$btc = $mixinSdk->Wallet()->readAsset("c6d0c728-2624-429b-8e0d-d9d19b6592fa");
print_r($btc);
```
## Instantly send Bitcoin to another Mixin Network account with zero cost
let's transfer the Bitcoin back to your Mixin Messenger user account for free and instantly.
```
$trans_info = $mixinSdk->Wallet()->transfer(BTC_ASSET_ID,$newConfig["client_id"],
                                         $mixinSdk->getConfig()['default']['pin'],AMOUNT);
print_r($trans_info);
```
![Confirm the result in Mixin messenger](https://github.com/wenewzhang/mixin_labs-php-bot/blob/master/newuser-transfer-bitcoin-to-me.jpg)
## Send Bitcoin to another Bitcoin address like normal Bitcoin transaction
Now let's withdraw Bitcoin to other exchange or wallet.

## Create withdrawal address and get the withdrawal fee
We need to add a Bitcoin withdrawal address by call [API](), the ID of address will be returned in result of API.
```php
$btcInfo = $mixinSdk->Wallet()->createAddress("c6d0c728-2624-429b-8e0d-d9d19b6592fa",
                                                    "14T129GTbXXPGXXvZzVaNLRFPeHXD1C25C",
                                                    $mixinSdk->getConfig()['default']['pin'],
                                                    "BTC withdral",false);
```
The **14T129GTbXXPGXXvZzVaNLRFPeHXD1C25C** if a Bitcoin wallet address, Output like below, fee is 0.0025738 BTC, The API result will give you the withdrawal address ID.                                                   
```php
Array
(
    [type] => address
    [address_id] => 345855b5-56a5-4f3b-ba9e-d99601ef86c1
    [asset_id] => c6d0c728-2624-429b-8e0d-d9d19b6592fa
    [public_key] => 14T129GTbXXPGXXvZzVaNLRFPeHXD1C25C
    [label] => BTC withdral
    [account_name] =>
    [account_tag] =>
    [fee] => 0.0025738
    [reserve] => 0
    [dust] => 0.0001
    [updated_at] => 2019-02-20T01:47:56.44067294Z
)
```
For EOS, the $label is the account_name, the others, $label just a memo.
```php
'account_name' => $label,
'account_tag'  => $public_key,
```
Now, commit the withdrawal request to Mixin Network, the $btcInfo["address_id"] is created above.
```php
$wdInfo->Wallet()->withdrawal($btcInfo["address_id"],
                            "0.01",
                            $mixinSdk->getConfig()['default']['pin'],
                            "BTC withdral");
```
#### Confirm the transction in blockchain explore

## Full example

For a general Mixin Network account, just can find the Mixin ID through Mixin Messenger, for example, my Mixin ID is 37222956,
```php
$userInfo = $mixinSdk->Network()->readUser("37222956");
$userInfo["user_id"];
print_r($userInfo);
```
So, i find my user id is 0b4f49dc-8fb4-4539-9a89-fb3afc613747,
```bash
Array
(
    [type] => user
    [user_id] => 0b4f49dc-8fb4-4539-9a89-fb3afc613747
    [identity_number] =>
    [full_name] => jimmyzhang
    [avatar_url] =>
    [relationship] => STRANGER
    [mute_until] => 2019-01-09T02:28:02.628864253Z
    [created_at] => 2018-11-29T04:47:33.018214877Z
    [is_verified] =>
)
```
Steps of this example:
 - **1.** Transfer 0.001 bitcoin to this bot through Mixin Messenger by manual.
 - **2.** Create a new user, and then get the Bitcoin wallet address.
 - **3.** Update the new user's PIN code.
 - **4.** Transfer 0.001 bitcoin to new user.
 - **5.** Transfer 0.001 bitcoin from new user to master.

Keep in mind change MASTER_ID to your's Mixin ID, or the coins will transfer to me, LOL!
```php
<?php
require __DIR__ . '/vendor/autoload.php';
use ExinOne\MixinSDK\MixinSDK;
$mixinSdk = new MixinSDK(require './config.php');

const PIN           = "945689";
const MASTER_ID     = "37222956";
const BTC_ASSET_ID  = "c6d0c728-2624-429b-8e0d-d9d19b6592fa";//bitcoin
// const ASSET_ID   = "965e5c6e-434c-3fa9-b780-c50f43cd955c";//CNB
const AMOUNT        = "0.001";

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
print_r($trans_info);
$asset_infoNew = $mixinSdkNew->Wallet()->readAsset(BTC_ASSET_ID);
print_r("BitCoin wallet address is :".$asset_infoNew["public_key"]);


$trans_info = $mixinSdk->Wallet()->transfer(BTC_ASSET_ID,$newConfig["client_id"],
                                         $mixinSdk->getConfig()['default']['pin'],AMOUNT);
print_r($trans_info);

```

![bitcoin-transfer](https://github.com/wenewzhang/mixin_labs-php-bot/blob/master/bitcoin-transfer-to-bot.jpg)

## Mixin Network support cryptocurrencies (2019-02-19)

|coin|uuid
|---|---
|EOS|6cfe566e-4aad-470b-8c9a-2fd35b49c68d
|CNB|965e5c6e-434c-3fa9-b780-c50f43cd955c
|BTC|c6d0c728-2624-429b-8e0d-d9d19b6592fa
|ETC|2204c1ee-0ea2-4add-bb9a-b3719cfff93a
|XRP|23dfb5a5-5d7b-48b6-905f-3970e3176e27
|XEM|27921032-f73e-434e-955f-43d55672ee31
|ETH|43d61dcd-e413-450d-80b8-101d5e903357
|DASH|6472e7e3-75fd-48b6-b1dc-28d294ee1476
|DOGE|6770a1e5-6086-44d5-b60f-545f9d9e8ffd
|LTC|76c802a2-7c88-447f-a93e-c29c9e5dd9c8
|SC|990c4c29-57e9-48f6-9819-7d986ea44985
|ZEN|a2c5d22b-62a2-4c13-b3f0-013290dbac60
|ZEC|c996abc9-d94e-4494-b1cf-2a3fd3ac5714
|BCH|fd11b6e3-0b87-41f1-a41f-f0e9b49e5bf0
