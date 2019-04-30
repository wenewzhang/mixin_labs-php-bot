# How to trade bitcoin through PHP
![](https://github.com/wenewzhang/mixin_labs-php-bot/raw/master/Bitcoin_php.jpg)

Exincore is introduced in [last chapter](https://github.com/wenewzhang/mixin_labs-php-bot/blob/master/README4.md), you can exchange many crypto asset at market price and receive your asset in 1 seconds. If you want to trade asset at limited price, or trade asset is not supported by ExinCore now, OceanOne is the answer.

## Solution Two: List your order on Ocean.One exchange
[Ocean.one](https://github.com/mixinNetwork/ocean.one) is a decentralized exchange built on Mixin Network, it's almost the first time that a decentralized exchange gain the same user experience as a centralized one.

You can list any asset on OceanOne. Pay the asset you want to sell to OceanOne account, write your request in payment memo, OceanOne will list your order to market. It send asset to your wallet after your order is matched.

* No sign up required
* No deposit required
* No listing process.

### Pre-request:
You should  have created a bot based on Mixin Network. Create one by reading [PHP Bitcoin tutorial](https://github.com/wenewzhang/mixin_labs-php-bot/blob/master/README.md).

#### Install required packages
[Chapter 4](https://github.com/wenewzhang/mixin_labs-php-bot/blob/master/README4.md) introduce [**mixin-sdk-php**](https://packagist.org/packages/exinone/mixin-sdk-php) to you, assume it has installed before.

#### Deposit USDT or Bitcoin into your Mixin Network account and read balance
The Ocean.one can match any order. Here we exchange between USDT and Bitcoin, Check the wallet's balance & address before you make order.

- Check the address & balance, find it's Bitcoin wallet address.
- Deposit Bitcoin to this Bitcoin wallet address.
- Check Bitcoin balance after 100 minutes later.

**Omni USDT address is same as Bitcoin address**

```php
  const BTC_ASSET_ID     = "c6d0c728-2624-429b-8e0d-d9d19b6592fa";
  const EOS_ASSET_ID     = "6cfe566e-4aad-470b-8c9a-2fd35b49c68d";
  const USDT_ASSET_ID    = "815b0b1a-2764-3736-8faa-42d694fa620a";

  $mixinSdk_eachAccountInstance = new MixinSDK(GenerateConfigByCSV($data));
  $asset_info = $mixinSdk_eachAccountInstance->Wallet()->readAsset(BTC_ASSET_ID);
  print_r("Bitcoin wallet address is :".$asset_info["public_key"]."\n");
  print_r("Bitcoin wallet balance is :".$asset_info["balance"]."\n");
```

#### Read orders book from Ocean.one
How to check the coin's price? You need understand what is the base coin. If you want buy Bitcoin and sell USDT, the USDT is the base coin. If you want buy USDT and sell Bitcoin, the Bitcoin is the base coin.


```php
if ( $ocmd == '1') { getOceanOneMarketInfos(XIN_ASSET_ID,USDT_ASSET_ID);}
function getOceanOneMarketInfos($targetCoin, $baseCoin)  {
  $client = new GuzzleHttp\Client();
  $baseUrl = "https://events.ocean.one/markets/".$targetCoin."-".$baseCoin."/book";
  $res = $client->request('GET', $baseUrl, [
      ]);
  if ($res->getStatusCode() == "200") {
    // echo $res->getStatusCode() . PHP_EOL;
    $resInfo = json_decode($res->getBody(), true);
    echo "Side | Price | Amount | Funds" . PHP_EOL;
    foreach ($resInfo["data"]["data"]["asks"] as $key => $exchange) {
      echo $exchange["side"] . " " . $exchange["price"] . " " . $exchange["amount"] ." " . $exchange["funds"] . PHP_EOL;
    }
    foreach ($resInfo["data"]["data"]["bids"] as $key => $exchange) {
      echo $exchange["side"] . " " . $exchange["price"] . " " . $exchange["amount"] ." " . $exchange["funds"] . PHP_EOL;
    }
  }
}
```

#### Create a memo to prepare order
The chapter two: [Echo Bitcoin](https://github.com/wenewzhang/mixin_labs-php-bot/blob/master/README2.md) introduce transfer coins. But you need to let Ocean.one know which coin you want to buy.
- **side** "B" or "A", "B" for buy, "A" for Sell.
- **asset** UUID of the asset you want to buy
- **price** If Side is "B", Price is AssetUUID; if Side is "A", Price is the asset which transfer to Ocean.one.

```php
function GenerateOrderMemo($side, $asset, $price) :string {
  $memo = base64_encode(MessagePack::pack([
                       'S' => $side,
                       'A' => Uuid::fromString($asset)->getBytes(),
                       'P' => $price,
                       'T' => 'L',
                       ]));
  return $memo;
}
```

#### Pay BTC to OceanOne with generated memo
Transfer Bitcoin(BTC_ASSET_ID) to Ocean.one(OCEANONE_BOT), put you target asset uuid(USDT) in the memo.

```php
const OCEANONE_BOT     = "aaff5bef-42fb-4c9f-90e0-29f69176b7d4";
const USDT_ASSET_ID    = "815b0b1a-2764-3736-8faa-42d694fa620a";
const XIN_ASSET_ID     = "c94ac88f-4671-3976-b60a-09064f1811e8";

if ( $ocmd == 's1') {
  $p = readline("Input the Price of XIN/USDT: ");
  $a = readline("Input the Amount of XIN: ");
  $tMemo = GenerateOrderMemo("A",USDT_ASSET_ID,$p);
  echo $tMemo .  PHP_EOL;
  $mixinSdk_WalletInstance = GenerateWalletSDKFromCSV();
  $asset_info = $mixinSdk_WalletInstance->Wallet()->readAsset(XIN_ASSET_ID);
  print_r($asset_info);
  if ( (float) $asset_info["balance"] >= (float) $a ) {
    $transInfos = $mixinSdk_WalletInstance->Wallet()->transfer(XIN_ASSET_ID,OCEANONE_BOT,
                                                $mixinSdk_WalletInstance->getConfig()['default']['pin'],
                                                $a,
                                                $tMemo);
    print_r($transInfos);
    echo "The Order ID (trace_id) is: " . $transInfos["trace_id"] . PHP_EOL;
  } else { echo "Not enough XIN!\n";}
}
```

If you want buy XIN, call it like below:

```php
if ( $ocmd == 'b1') {
  $p = readline("Input the Price of XIN/USDT: ");
  $a = readline("Input the Amount of USDT: ");
  $tMemo = GenerateOrderMemo("B",XIN_ASSET_ID,$p);
  echo $tMemo .  PHP_EOL;
  $mixinSdk_WalletInstance = GenerateWalletSDKFromCSV();
  $asset_info = $mixinSdk_WalletInstance->Wallet()->readAsset(USDT_ASSET_ID);

  print_r($asset_info);
  if ( ((float) $asset_info["balance"] >= 1) && ( (float) $asset_info["balance"] >= (float) $a ) ) {
    $transInfos = $mixinSdk_WalletInstance->Wallet()->transfer(USDT_ASSET_ID,OCEANONE_BOT,
                                                $mixinSdk_WalletInstance->getConfig()['default']['pin'],
                                                $a,
                                                $tMemo);
    print_r($transInfos);
    echo "The Order ID (trace_id) is: " . $transInfos["trace_id"] . PHP_EOL;
  } else { echo "Not enough USDT!\n";}
}
```

A success order output like below:
```bash
Input the Price of XIN/USDT: 112
Input the Amount of USDT: 1
hKFToUKhQcQQyUrIj0ZxOXa2CgkGTxgR6KFQozExMqFUoUw=
client id is:26b20aa5-40c0-3e00-9de0-666cfb6f2daa
Array
(
    [type] => asset
    [asset_id] => 815b0b1a-2764-3736-8faa-42d694fa620a
    [chain_id] => c6d0c728-2624-429b-8e0d-d9d19b6592fa
    [symbol] => USDT
    [name] => Tether USD
    [icon_url] => https://images.mixin.one/ndNBEpObYs7450U08oAOMnSEPzN66SL8Mh-f2pPWBDeWaKbXTPUIdrZph7yj8Z93Rl8uZ16m7Qjz-E-9JFKSsJ-F=s128
    [balance] => 1
    [public_key] => 17z1Rq3VsyvvXvGWiHT8YErjBoFgnhErB8
    [account_name] =>
    [account_tag] =>
    [price_btc] => 0.00019038
    [price_usd] => 1.00036293
    [change_btc] => 0.013486479778200063
    [change_usd] => 0.005376748815937048
    [asset_key] => 815b0b1a-2764-3736-8faa-42d694fa620a
    [confirmations] => 6
    [capitalization] => 0
)
Array
(
    [type] => transfer
    [snapshot_id] => f4b1f8d6-004a-4d2b-997d-4d0acf1096cd
    [opponent_id] => aaff5bef-42fb-4c9f-90e0-29f69176b7d4
    [asset_id] => 815b0b1a-2764-3736-8faa-42d694fa620a
    [amount] => -1
    [trace_id] => b12eed67-6cf4-481f-b25b-dd41f28e1984
    [memo] => hKFToUKhQcQQyUrIj0ZxOXa2CgkGTxgR6KFQozExMqFUoUw=
    [created_at] => 2019-04-30T01:17:02.206240549Z
    [counter_user_id] => aaff5bef-42fb-4c9f-90e0-29f69176b7d4
)
The Order ID (trace_id) is: b12eed67-6cf4-481f-b25b-dd41f28e1984
```
## Cancel the Order
To cancel order, just pay any amount of any asset to OceanOne, and write trace_id into memo. Ocean.one take the trace_id as the order id, for example, **b12eed67-6cf4-481f-b25b-dd41f28e1984** is a order id,
We can use it to cancel the order.

```php
if ( $ocmd == 'c' ) {
  $mixinSdk_WalletInstance = GenerateWalletSDKFromCSV();
  $asset_info = $mixinSdk_WalletInstance->Wallet()->readAsset(CNB_ASSET_ID);
  if ( ((float) $asset_info["balance"] == 0) ) {
     echo "Please deposit some CNB to this Wallet!" . PHP_EOL;
  } else {
     $orderid = readline("Input the Order id ( trace_id ): ");
     $cMemo =  base64_encode(MessagePack::pack([
                           'O' => Uuid::fromString($orderid)->getBytes(),
                           ]));
     $transInfos = $mixinSdk_WalletInstance->Wallet()->transfer(CNB_ASSET_ID,OCEANONE_BOT,
                                                 $mixinSdk_WalletInstance->getConfig()['default']['pin'],
                                                 "0.00000001",
                                                 $cMemo);
     print_r($transInfos);
  }
}
```
#### Read Bitcoin balance
Check the wallet's balance.
```php
if ($line == 'aw') {
  $mixinSdk_eachAccountInstance = GenerateWalletSDKFromCSV();
  $asset_info = $mixinSdk_eachAccountInstance->Wallet()->readAssets();
  foreach ($asset_info as $key => $asset) {
    echo  $asset["symbol"] . "   " . $asset["asset_id"] ."   ". $asset["balance"] .
          "   ". $asset["public_key"].PHP_EOL;
  }
}
```

## Source code usage
Build it and then run it.

- **php bitcoin_wallet.php** run it.

Commands of trade with OceanOne:

- o: Ocean.One Exchange
- q: Exit

Make your choose(eg: q for Exit!):

- 1:  Fetch XIN/USDT orders
- s1: Sell XIN/USDT
- b1: Buy XIN/USDT
- 2:  Fetch ERC20(Benz)/USDT orders
- s2: Sell Benz/USDT
- b2: Buy Benz/USDT
- q:  Exit

[Full source code](https://github.com/wenewzhang/mixin_labs-php-bot/blob/master/bitcoin_wallet.php)
