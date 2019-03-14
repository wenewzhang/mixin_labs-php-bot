# How to trade bitcoin through PHP language

## Solution One: pay to ExinCore API
[Exincore](https://github.com/exinone/exincore) provide a commercial trading API on Mixin Network.

You pay USDT to ExinCore, ExinCore transfer Bitcoin to you on the fly with very low fee and fair price. Every transaction is anonymous to public but still can be verified on blockchain explorer. Only you and ExinCore know the details.

ExinCore don't know who you are because ExinCore only know your client's uuid.

### Pre-request:
You should already have created a wallet based on Mixin Network. Create one by reading [PHP Bitcoin tutorial](https://github.com/wenewzhang/mixin_labs-php-bot).

#### Install required packages
As you know, we introduce you the mixin-sdk-php in [chapter 1](https://github.com/wenewzhang/mixin_labs-php-bot/blob/master/README.md), assume it has installed before, let's install **uuid, msgpack** here.
```bash
  composer require ramsey/uuid
  composer require rybakit/msgpack
```
#### Deposit USDT or Bitcoin into your Mixin Network account(bot) and read balance
ExinCore can exchange between Bitcoin, USDT, EOS, Eth etc. Here show you how to exchange between USDT and Bitcoin,
you need check the wallet's balance.
```php
  $mixinSdk = new MixinSDK(require './config.php');
  $asset_info = $mixinSdk->Wallet()->readAsset(BTC_ASSET_ID);
  print_r("Bitcoin wallet balance is :".$asset_info["balance"]."\n");
  $asset_info = $mixinSdk->Wallet()->readAsset(USDT_ASSET_ID);
  print_r("USDT wallet balance is :".$asset_info["balance"]."\n");
```
#### Read market price
How to check the coin's price? you need know which is the base coin,
for example, if you want buy Bitcoin and sell USDT, the USDT is the base coin, Also,
if you want buy USDT and sell Bitcoin, the Bitcoin is the base coin, source code like below.
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
![Bitcoint wallet balance](https://github.com/wenewzhang/mixin_labs-php-bot/blob/master/btc-usdt-price.jpg)

#### Create a memo to prepare order
In the chapter two: [Echo Bitcoin](https://github.com/wenewzhang/mixin_labs-php-bot/blob/master/README2.md) introduce you
transfer coins, ExinCore not only want know the coin you transferred, but also want know which coin you want to buy, the answer is memo, put you target asset id in memo.
```php
$memo = base64_encode(MessagePack::pack([
                     'A' => Uuid::fromString($_targetAssetID)->getBytes(),
                     ]));
```
#### Pay BTC to API gateway with generated memo
Transfer Bitcoin(BTC_ASSET_ID) to ExinCore(EXIN_BOT), put you target asset uuid in the memo, otherwise, ExinCore will refund you coin immediately!
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
The ExinCore should transfer the target coin to your bot, meanwhile, put the fee, order id, price etc. information in the memo, unpack the data like below.
```php
echo "------------MEMO:-coin--exchange--------------" . PHP_EOL;
// print_r($dtPay->memo);
echo "You Get Coins: ". $dtPay->asset_id. " " . $dtPay->amount . PHP_EOL;
$memoUnpack = MessagePack::unpack(base64_decode($dtPay->memo));
$feeAssetID = Uuid::fromBytes($memoUnpack['FA'])->toString();
$OrderID    = Uuid::fromBytes($memoUnpack['O'])->toString();
if ($memoUnpack['C'] == 1000) {
  echo "Successful Exchange:". PHP_EOL;
  echo "Fee asset ID: " . $feeAssetID . " fee is :" . $memoUnpack['F'] . PHP_EOL;
  echo "Order ID: " . $OrderID . " Price is :" . $memoUnpack['P'] . PHP_EOL;
} else print_r($memoUnpack);
```

If you coin exchange successful, console output like below:
```bash
------------MEMO:-coin--exchange--------------
You Get Coins: 815b0b1a-2764-3736-8faa-42d694fa620a 0.3852528
Successful Exchange:
Fee asset ID: 815b0b1a-2764-3736-8faa-42d694fa620a fee is :0.0007736
Order ID: f49124fe-fc53-46d0-bed8-57bc0c3bf893 Price is :3868.09
```

#### Read Bitcoin balance
Check the wallet's balance.
```php
$mixinSdk = new MixinSDK(require './config.php');
$asset_info = $mixinSdk->Wallet()->readAsset(USDT_ASSET_ID);
print_r("USDT wallet balance is :".$asset_info["balance"]."\n");
```
![Bitcoint wallet balance](https://github.com/wenewzhang/mixin_labs-php-bot/blob/master/exchange-and-balance.jpg)

#### Advanced usage
Some time, you don't want exchange coin through bot, you can pay coin to ExinCore 's bot directly,
But need your bot create a payment link for you! Here create a APP_CARD link which pay 0.0001 bitcoin to EXIN_BOT,
target coin is USDT.
```php
function sendAppCardBuyUSDTSellBTC($jsMsg):Array
{
  $client_id = (require "./config.php")['client_id'];
  $memo = base64_encode(MessagePack::pack([
                       'A' => Uuid::fromString('815b0b1a-2764-3736-8faa-42d694fa620a')->getBytes(),
                       ]));
   $payLink = "https://mixin.one/pay?recipient=".
                EXIN_BOT."&asset=".
                "c6d0c728-2624-429b-8e0d-d9d19b6592fa".
                "&amount=0.0001"."&trace=".Uuid::uuid4()->toString().
                "&memo=".$memo;
   $msgData = [
       'icon_url'    =>  "https://mixin.one/assets/98b586edb270556d1972112bd7985e9e.png",
       'title'       =>  "Pay 0.0001 BTC",
       'description' =>  "pay",
       'action'      =>  $payLink,
   ];
   $msgParams = [
     'conversation_id' => $jsMsg->data->conversation_id,// $callTrait->config[client_id],
     // 'recipient_id'    => $jsMsg->data->user_id,
     'category'        => 'APP_CARD',//'PLAIN_TEXT',
     'status'          => 'SENT',
     'message_id'      => Uuid::uuid4()->toString(),
     'data'            => base64_encode(json_encode($msgData)),//base64_encode("hello!"),
   ];
   $msgPayButton = [
     'id'     =>  Uuid::uuid4()->toString(),
     'action' =>  'CREATE_MESSAGE',
     'params' =>   $msgParams,
   ];
   return $msgPayButton;
}
```
![](https://github.com/wenewzhang/mixin_labs-php-bot/blob/master/user-exchange-bitcoin-directly.jpg)

The ExinCore 's bot pay USDT back!

![](https://github.com/wenewzhang/mixin_labs-php-bot/blob/master/user-directly-exchage-result.jpg)

## Solution Two: List your order on Ocean.One exchange
