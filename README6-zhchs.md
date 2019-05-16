# 用PHP在去中心化交易所OceanOne上挂单买卖任意ERC20 token
![](https://github.com/wenewzhang/mixin_labs-php-bot/raw/master/php-eth.jpg)

在[上一课](https://github.com/wenewzhang/mixin_labs-php-bot/blob/master/README5.md)中，我们介绍了如何在OceanOne交易比特币。OceanOne支持交易任何Mixin Network上的token，包括所有的ERC20和EOS token，不需要任何手续和费用，直接挂单即可。下面介绍如何将将一个ERC20 token挂上OceanOne交易！在掌握了ERC20 token之后，就可以把任何token在Ocean上买卖。

此处我们用一个叫做Benz的[ERC20 token](https://etherscan.io/token/0xc409b5696c5f9612e194a582e14c8cd41ecdbc67)为例。这个token已经被充值进Mixin Network，你可以在[区块链浏览器](https://mixin.one/snapshots/2b9c216c-ef60-398d-a42a-eba1b298581d )看到这个token在Mixin Network内部的总数和交易
### 预备知识:
先将Benz币存入你的钱包，然后使用**getAssets** API读取它的UUID.

### 取得该币的UUID
调用 **getAssets** API 会返回json数据, 如:

- **asset_id** 币的UUID.
- **public_key** 该币的当前钱包的地址.
- **symbol**  币的名称. 如: Benz.

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
调用 **getAssets** API的完整输出如下:
```bash
Make your choose:aw
run...
client id is:26b20aa5-40c0-3e00-9de0-666cfb6f2daa
Benz   2b9c216c-ef60-398d-a42a-eba1b298581d   799   0x9A4F6c67444cd6558905ef5B04a4c429b9538A9d
EOS   6cfe566e-4aad-470b-8c9a-2fd35b49c68d   0
CNB   965e5c6e-434c-3fa9-b780-c50f43cd955c   4.72599997   0x9A4F6c67444cd6558905ef5B04a4c429b9538A9d
BTC   c6d0c728-2624-429b-8e0d-d9d19b6592fa   0   17z1Rq3VsyvvXvGWiHT8YErjBoFgnhErB8
XIN   c94ac88f-4671-3976-b60a-09064f1811e8   0.01   0x9A4F6c67444cd6558905ef5B04a4c429b9538A9d
```
### 限价挂单
- **挂限价买单**  低于或者等于市场价的单.
- **挂限价卖单**  高于或者是等于市场价的单.

OceanOne支持三种基类价格: USDT, XIN, BTC, 即: Benz/USDT, Benz/XIN, Benz/BTC, 这儿示范Benz/USDT.

### 限价挂卖单.
新币挂单后,需要等一分钟左右，等OceanOne来初始化新币的相关数据.

```php
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

### 限价挂买单.
新币挂单后,需要等一分钟左右，等OceanOne来初始化新币的相关数据.

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
### 读取币的价格列表
读取币的价格列表，来确认挂单是否成功!

```php
    if ( $ocmd == '2') { getOceanOneMarketInfos(ERC20_BENZ,USDT_ASSET_ID);}
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
### ERC20相关的操作指令

Commands list of this source code:

- trb:Transfer ERC20 from Bot to Wallet
- trm:Transfer ERC20 from Wallet to Master
- o: Ocean.One Exchange

Make your choose(eg: q for Exit!):
- x:  Orders-Book of ERC20/USDT
- x1: Buy ERC20 pay USDT
- x2: Sell ERC20 get USDT
- c: Cancel the order
- q: Exit

[完整的代码](https://github.com/wenewzhang/mixin_labs-php-bot/blob/master/bitcoin_wallet.php)
