In last two chapters, we create a bot to [receive user's message and send message back to user](https://github.com/wenewzhang/mixin_labs-php-bot/blob/master/README.md), the bot can [receive Bitcoin and send it back to user](https://github.com/wenewzhang/mixin_labs-php-bot/blob/master/README2.md).


# What you will learn from this chapter
1. How to create Bitcoin wallet by Mixin Network PHP SDK
2. How to read Bitcoin balance by Mixin Network PHP SDK
3. How to send Bitcoin with zero transaction fee and confirmed in 1 second by Mixin Network PHP SDK
4. How to send Bitcoin to other wallet by Mixin Network PHP SDK

## Create a Bitcoin wallet by Mixin Network PHP SDK
### Pre-request: Create a Mixin Network account
```php
$user_info = $mixinSdk->Network()->createUser("Tom cat");
```
The function in PHP SDK create a RSA keypair automatically, then call Mixin Network to create an account. Then the function return all account information.

```php
//Create User api include all account information
print_r($user_info);
print($user_info["pubKey"]);
$newConfig = array();
$newConfig["private_key"] = $user_info["priKey"];
$newConfig["pin_token"]   = $user_info["pin_token"];
$newConfig["session_id"]  = $user_info["session_id"];
$newConfig["client_id"]   = $user_info["user_id"];
```
You will find that the parameter has same name with app's parameter generated in dashboard. All account in Mixin network use same rule to communicate with Mixin Network. There are some small differences: developer account can only create app in dashboard, and app can only create account by API.

Now you need to keep the account information and secure.

### Create Bitcoin wallet for the Mixin Network account
The Bitcoin  wallet is not generated automatically at same time when we create Mixin Network account. We need to create one by read Bitcoin asset once.
```php
$asset_infoNew = $mixinSdkNew->Wallet()->readAsset("c6d0c728-2624-429b-8e0d-d9d19b6592fa");
echo "BitCoin wallet address is :".$asset_infoNew["public_key"];
```
You can found information about Bitcoin asset in the account. Public key is the Bitcoin deposit address. Full response of read asset is
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
The API provide many information about Bitcoin asset. Logo, price in USD, price change in USD. Your Bitcoin deposit will be accepted by Mixin Network only after your deposit is confirmed by 12 Bitcoin blocks. 

### Private key?
You may ask where is Bitcoin private key? The private key is protected by multi signature inside Mixin Network so it is invisible for user. Bitcoin asset can only be withdraw to other address when user provide correct RSA private key signature, PIN code and Session key.

### Not only Bitcoin, but also Ethereum, EOS
The account not only contain a Bitcoin wallet, but also contains wallet for Ethereum, EOS, etc. Full blockchain support [list](https://mixin.one/network/chains). All ERC20 Token and EOS token are supported by the account.

Create other asset wallet is same as create Bitcoin wallet, just read the asset.

### Deposit Bitcoin and read Bitcoin balance
Now you can deposit some bitcoin into the Bitcoin deposit address from other exchange or wallet. This is maybe too expensive for this tutorial. There is a free and lightening fast solution to deposit Bitcoin : Add BTC deposit address in your Mixin messenger account and withdraw small amount Bitcoin from your account to the address. It is free and confirmed instantly because they are both on Mixin Network. 

Now you can read Bitcoin balance of the account again to confirm the action.
```php
$btc = $mixinSdk->Wallet()->readAsset("c6d0c728-2624-429b-8e0d-d9d19b6592fa");
print_r($btc);
```
### Instantly send Bitcoin to another Mixin Network account with zero cost
#### Pre-request:  Create PIN for account
A PIN is required to send any asset in Mixin Network. Let's create pin for the account.
```php
//Create a PIN.
$pinInfo = $mixinSdkNew->Pin()->updatePin('',PIN);
print_r($pinInfo);
```
#### Send Bitcoin with 0 transaction fee and confirm it instantly
```php
$trans_info = $mixinSdk->Wallet()->transfer(BTC_ASSET_ID,$newConfig["client_id"],
                                         $mixinSdk->getConfig()['default']['pin'],AMOUNT);
print_r($trans_info);
```
![Confirm the result in Mixin messenger](https://github.com/wenewzhang/mixin_labs-php-bot/blob/master/newuser-transfer-bitcoin-to-me.jpg)
### Send Bitcoin to another Bitcoin exchange or wallet
If you want to send Bitcoin to another exchange or wallet, you need to know the destination deposit address, then add the address in withdraw address list of the Mixin network account.

#### Pre-request: Add withdrawal address and get the withdrawal fee
We need to add a Bitcoin withdrawal address by call [API](), the ID of address will be returned in result of API.
```php
$btcInfo = $mixinSdk->Wallet()->createAddress("c6d0c728-2624-429b-8e0d-d9d19b6592fa",
                                                    "14T129GTbXXPGXXvZzVaNLRFPeHXD1C25C",
                                                    $mixinSdk->getConfig()['default']['pin'],
                                                    "BTC withdral",false);
```
The **14T129GTbXXPGXXvZzVaNLRFPeHXD1C25C** is a Bitcoin wallet address, Output like below, fee is 0.0025738 BTC, The API result will give you the withdrawal address ID.                                                   
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

#### Read withdraw fee anytime
```php
$wdInfo->Wallet()->readAddress($btcInfo["address_id"]);
```

#### Send Bitcoin to destination address
Submit the withdrawal request to Mixin Network, the $btcInfo["address_id"] is the address id created above.
```php
$wdInfo->Wallet()->withdrawal($btcInfo["address_id"],
                            "0.01",
                            $mixinSdk->getConfig()['default']['pin'],
                            "BTC withdral");
```
#### Confirm the transction in blockchain explore

## Full example
```php
$wdInfo->Wallet()->withdrawal($btcInfo["address_id"],
                            "0.01",
                            $mixinSdk->getConfig()['default']['pin'],
                            "BTC withdral");
```
[Full source code]()


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
