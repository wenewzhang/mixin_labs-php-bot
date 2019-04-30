# 通过 PHP 买卖Bitcoin
![](https://github.com/wenewzhang/mixin_labs-php-bot/raw/master/Bitcoin_php.jpg)
上一章介绍了[Exincore](https://github.com/wenewzhang/mixin_labs-php-bot/blob/master/README4-zhchs.md)，你可以1秒完成资产的市价买卖。如果你想限定价格买卖，或者买卖一些exincore不支持的资产，你需要OceanOne。

## 方案二: 挂单Ocean.One交易所
[Ocean.one](https://github.com/mixinNetwork/ocean.one)是基于Mixin Network的去中心化交易所，它性能一流。
你可以在OceanOne上交易任何资产，只需要将你的币转给OceanOne, 将交易信息写在交易的memo里，OceanOne会在市场里列出你的交易需求，
交易成功后，会将目标币转入到你的MixinNetwork帐上，它有三大特点与优势：
- 不需要在OceanOne注册
- 不需要存币到交易所
- 支持所有Mixin Network上能够转账的资产，所有的ERC20 EOS代币。

### 预备知识:
你先需要创建一个机器人, 方法在 [教程一](https://github.com/wenewzhang/mixin_labs-php-bot/blob/master/README-zhchs.md).

#### 安装依赖包
正如教程一里我们介绍过的， 我们需要依赖 [**mixin-sdk-php**](https://packagist.org/packages/exinone/mixin-sdk-php), 你应该已经先安装过它了.

#### 安装依赖的库
[第四课](https://github.com/wenewzhang/mixin_labs-php-bot/blob/master/README4-zhchs.md), 在上一课中已经安装好了.

#### 充币到 Mixin Network, 并读出它的余额.
此处演示用 USDT购买BTC 或者 用BTC购买USDT。交易前，先检查一下钱包地址。
完整的步骤如下:
- 检查比特币或USDT的余额，钱包地址。并记下钱包地址。
- 从第三方交易所或者你的冷钱包中，将币充到上述钱包地址。
- 再检查一下币的余额，看到帐与否。(比特币的到帐时间是5个区块的高度，约100分钟)。

比特币与USDT的充值地址是一样的。

```php
  const BTC_ASSET_ID     = "c6d0c728-2624-429b-8e0d-d9d19b6592fa";
  const EOS_ASSET_ID     = "6cfe566e-4aad-470b-8c9a-2fd35b49c68d";
  const USDT_ASSET_ID    = "815b0b1a-2764-3736-8faa-42d694fa620a";

  $mixinSdk_eachAccountInstance = new MixinSDK(GenerateConfigByCSV($data));
  $asset_info = $mixinSdk_eachAccountInstance->Wallet()->readAsset(BTC_ASSET_ID);
  print_r("Bitcoin wallet address is :".$asset_info["public_key"]."\n");
  print_r("Bitcoin wallet balance is :".$asset_info["balance"]."\n");
```

#### 取得Ocean.one的市场价格信息
如何来查询Ocean.one市场的价格信息呢？你要先了解你交易的基础币是什么，如果你想买比特币，卖出USDT,那么基础货币就是USDT;如果你想买USDT,卖出比特币，那么基础货币就是比特币.

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

#### 交易前，创建一个Memo!
在第二章里,[基于Mixin Network的 PHP 比特币开发教程: 机器人接受比特币并立即退还用户](https://github.com/wenewzhang/mixin_labs-php-bot/blob/master/README2-zhchs.md), 我们学习过转帐，这儿我们介绍如何告诉Ocean.one，我们给它转帐的目的是什么，信息全部放在memo里.
- **side** 方向,"B" 或者 "A", "B"是购买, "A"是出售.
- **asset** 目标虚拟资产的UUID.
- **price** 价格，如果操作方向是"B", 价格就是AssetUUID的价格; 如果操作方向是"B", 价格就是转给Ocean.one币的价格.

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

#### 出售XIN的例子
转打算出售的XIN给Ocean.one(OCEANONE_BOT),将你打算换回来的目标虚拟资产的UUID放入memo.

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
如果你是打算买XIN,操作如下:

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

一个成功的挂单如下：
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
#### 取消挂单
Ocean.one将trace_id当做订单，比如上面的例子， **b12eed67-6cf4-481f-b25b-dd41f28e1984** 就是订单号，我们用他来取消订单。

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
#### 通过读取资产余额，来确认到帐情况
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

## 源代码执行
运行就可以开始交易了.

- **php bitcoin_wallet.php**  运行项目

本代码执行时的命令列表:

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

[完整代码](https://github.com/wenewzhang/mixin_labs-php-bot/blob/master/bitcoin_wallet.php)
