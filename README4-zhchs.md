# 如何用 PHP 进行Bitcoin交易!

## 方案一: 通过ExinCore API进行币币交易
[Exincore](https://github.com/exinone/exincore) 提供了基于Mixin Network的币币交易API.

你可以支付USDT给ExinCore, ExinCore会以最低的价格，最优惠的交易费将你购买的比特币转给你, 每一币交易都是匿名的，并且可以在区块链上进行验证，交易的细节只有你与ExinCore知道！

ExinCore 也不知道你是谁，它只知道你的UUID.

### 预备知识:
你先需要创建一个机器人, 方法在 [教程一](https://github.com/wenewzhang/mixin_labs-php-bot/blob/master/README-zhchs.md).

#### 安装依赖包
正如教程一里我们介绍过的， 我们需要依赖 **mixin-sdk-php**, 你应该先安装过它了， 这儿我们再安装 **uuid, msgpack** 两个软件包.
```bash
  composer require ramsey/uuid
  composer require rybakit/msgpack
```
#### 充币到 Mixin Network, 并读出它的余额.
ExinCore可以进行BTC, USDT, EOS, ETH 等等交易， 这儿演示如果用 USDT购买BTC 或者 用BTC购买USDT, 交易前，先检查一下钱包地址！
完整的步骤如下:
- 检查比特币或USDT的余额，钱包地址。并记下钱包地址。
- 从第三方交易所或者你的冷钱包中，将币充到上述钱包地址。
- 再检查一下币的余额，看到帐与否。(比特币的到帐时间是5个区块的高度，约100分钟)。
**请注意，比特币与USDT的地址是一样的。
```php
if ($line == '2') {
  if (($handle = fopen("new_users.csv", "r")) !== FALSE) {
  while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
    $mixinSdk_eachAccountInstance = new MixinSDK(GenerateConfigByCSV($data));
    $asset_info = $mixinSdk_eachAccountInstance->Wallet()->readAsset(BTC_ASSET_ID);
    print_r("Bitcoin wallet address is :".$asset_info["public_key"]."\n");
    print_r("Bitcoin wallet balance is :".$asset_info["balance"]."\n");
  }
    fclose($handle);
  } else print("Create user first\n");
}
if ($line == '3') {
  if (($handle = fopen("new_users.csv", "r")) !== FALSE) {
  while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
    $mixinSdk_eachAccountInstance = new MixinSDK(GenerateConfigByCSV($data));
    $asset_info = $mixinSdk_eachAccountInstance->Wallet()->readAsset(USDT_ASSET_ID);
    print_r("USDT wallet address is :".$asset_info["public_key"]."\n");
    print_r("USDT wallet balance is :".$asset_info["balance"]."\n");
  }
    fclose($handle);
  } else print("Create user first\n");
}
```
#### 查询ExinCore市场的价格信息
如果来查询ExinCore市场的价格信息呢？你要先了解你交易的基础币是什么，如果你想买比特币，卖出USDT,那么基础货币就是USDT;如果你想买USDT,卖出比特币，那么基础货币就是比特币.
```php
function getExchangeCoins($base_coin) :string {
  $client = new GuzzleHttp\Client();
  $res = $client->request('GET', 'https://exinone.com/exincore/markets?base_asset='.$base_coin, [
      ]);
  $result = "";
  if ($res->getStatusCode() == "200") {
    // echo $res->getStatusCode() . PHP_EOL;
    $resInfo = json_decode($res->getBody(), true);
    echo "Asset ID | Asset Symbol | Price | Amount | Exchanges" . PHP_EOL;
    $result = "Asset ID | Asset Symbol | Price | Amount | Exchanges" . PHP_EOL;
    foreach ($resInfo["data"] as $key => $coinInfo) {
      echo ($coinInfo["exchange_asset"] ." ".$coinInfo["exchange_asset_symbol"]. "/". $coinInfo["base_asset_symbol"] .
            " ". $coinInfo["price"] ." ". $coinInfo["minimum_amount"] ."-". $coinInfo["maximum_amount"] . " ");
      $result .= $coinInfo["exchange_asset_symbol"]. "/". $coinInfo["base_asset_symbol"] .
                  " ". $coinInfo["price"] ." ". $coinInfo["minimum_amount"] ."-". $coinInfo["maximum_amount"] . " ";
      foreach ($coinInfo["exchanges"] as $key => $exchange) {
        echo $exchange . " ";
        $result .= $exchange . " ";
      }
      echo PHP_EOL;
      $result .= PHP_EOL;
    }
  }
  return $result;
}
```

#### 交易前，创建一个Memo!
在第二章里,[基于Mixin Network的PHP比特币开发教程: 机器人接受比特币并立即退还用户](https://github.com/wenewzhang/mixin_labs-php-bot/blob/master/README2-zhchs.md), 我们学习过退还用户比特币，在这里，我们除了给ExinCore支付币外，还要告诉他我们想购买的币是什么，即将想购买的币存到memo里。
```php
$memo = base64_encode(MessagePack::pack([
                     'A' => Uuid::fromString($_targetAssetID)->getBytes(),
                     ]));
```
#### 币币交易的完整流程
转币给ExinCore时，将memo写入你希望购买的币，否则，ExinCore会直接退币给你！
```php
const EXIN_BOT        = "61103d28-3ac2-44a2-ae34-bd956070dab1";
const BTC_ASSET_ID    = "c6d0c728-2624-429b-8e0d-d9d19b6592fa";
const EOS_ASSET_ID    = "6cfe566e-4aad-470b-8c9a-2fd35b49c68d";
const USDT_ASSET_ID   = "815b0b1a-2764-3736-8faa-42d694fa620a";
coinExchange(BTC_ASSET_ID,"0.0001",USDT_ASSET_ID);

//...........

function coinExchange($_assetID,$_amount,$_targetAssetID) {
  $mixinSdk = new MixinSDK(require './config.php');
  // print_r();
  $memo = base64_encode(MessagePack::pack([
                       'A' => Uuid::fromString($_targetAssetID)->getBytes(),
                       ]));
  $BotInfo = $mixinSdk->Wallet()->transfer($_assetID,EXIN_BOT,
                                           $mixinSdk->getConfig()['default']['pin'],$_amount,$memo);
  print_r($BotInfo);
}
```
交易完成后，Exincore会将你需要的币转到你的帐上，同样，会在memo里，记录成交价格，交易费用等信息！你只需要按下面的方式解开即可！
```php
$limit        = 20;
$offset       = '2019-03-10T01:58:25.362528Z';
$snapInfo = $mixinSdk_BotInstance->Wallet()->readUserSnapshots($limit, $offset);
// print_r($networkInfo2);
foreach ($snapInfo as  $record) {
  // echo $key . PHP_EOL;
  // print_r($record);
  if ($record['amount'] > 0 and $record['memo'] != '') {
    echo "------------MEMO:-coin--exchange--------------" . PHP_EOL;
    echo "memo: " . $record['memo'] . PHP_EOL;
    // print_r($dtPay->memo);
    echo "You Get Coins: ". $record['asset_id']. " " . $record['amount'] . PHP_EOL;
    $memoUnpack = MessagePack::unpack(base64_decode($record['memo']));
    $feeAssetID = Uuid::fromBytes($memoUnpack['FA'])->toString();
    $OrderID    = Uuid::fromBytes($memoUnpack['O'])->toString();
    if ($memoUnpack['C'] == 1000) {
      echo "Successful Exchange:". PHP_EOL;
      echo "Fee asset ID: " . $feeAssetID . " fee is :" . $memoUnpack['F'] . PHP_EOL;
      echo "Order ID: " . $OrderID . " Price is :" . $memoUnpack['P'] . PHP_EOL;
    } else print_r($memoUnpack);
    echo "--------------memo-record end---------------" . PHP_EOL;
  }
}
```

一次成功的交易如下：
```bash
------------MEMO:-coin--exchange--------------
memo: hqFDzQPooVCnMzg3Mi45N6FGqTAuMDAwNzc0NqJGQcQQgVsLGidkNzaPqkLWlPpiCqFUoUahT8QQIbfeL6p5RVOcEP0mLb+t+g==
You Get Coins: 815b0b1a-2764-3736-8faa-42d694fa620a 0.3857508
Successful Exchange:
Fee asset ID: 815b0b1a-2764-3736-8faa-42d694fa620a fee is :0.0007746
Order ID: 21b7de2f-aa79-4553-9c10-fd262dbfadfa Price is :3872.97
--------------memo-record end---------------
```

#### 读取币的余额
通过读取币的余额，来确认交易情况！
```php
$mixinSdk = new MixinSDK(require './config.php');
$asset_info = $mixinSdk->Wallet()->readAsset(USDT_ASSET_ID);
print_r("USDT wallet balance is :".$asset_info["balance"]."\n");
```
## 源代码执行
执行 **php call_apis.php** 即可开始交易了.

- 1: Create user and update PIN
- 2: Read Bitcoin balance & address
- 3: Read USDT balance & address
- 4: Read EOS balance
- 5: Read EOS address
- 6: Transfer Bitcoin from bot to new user
- 7: Transfer Bitcoin from new user to Master
- 8: Withdraw bot's Bitcoin
- 9: Withdraw bot's EOS
- qu: Read market price(USDT)
- qb: Read market price(BTC)
- b: Balance of  bot (USDT & BTC)
- s: Read Snapshots
- tb: Transfer 0.0001 BTC buy USDT
- tu: Transfer $1 USDT buy BTC
- q: Exit

[完整代码](https://github.com/wenewzhang/mixin_labs-php-bot/blob/master/call_apis.php)

## Solution Two: List your order on Ocean.One exchange
