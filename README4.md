# How to trade bitcoin through PHP language

## Solution One: pay to ExinCore API
[Exincore](https://github.com/exinone/exincore) provide a commercial trading API on Mixin Network.

You pay USDT to ExinCore, ExinCore transfer Bitcoin to you on the fly with very low fee and fair price. Every transaction is anonymous to public but still can be verified on blockchain explorer. Only you and ExinCore know the details.

ExinCore don't know who you are because ExinCore only know your client's uuid.

### Pre-request:
You should already have created a wallet based on Mixin Network. Create one by reading [PHP Bitcoin tutorial](https://github.com/wenewzhang/mixin_labs-php-bot).

#### Install required packages
```bash
  composer require ramsey/uuid
  composer require rybakit/msgpack
```
#### Deposit USDT into your Mixin Network account(bot) and read balance
```php
  $mixinSdk = new MixinSDK(require './config.php');
  $asset_info = $mixinSdk->Wallet()->readAsset(BTC_ASSET_ID);
  print_r("Bitcoin wallet balance is :".$asset_info["balance"]."\n");
  $asset_info = $mixinSdk->Wallet()->readAsset(USDT_ASSET_ID);
  print_r("USDT wallet balance is :".$asset_info["balance"]."\n");
```
#### read market price
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
#### Create a memo to prepare order
```php
$memo = base64_encode(MessagePack::pack([
                     'A' => Uuid::fromString($_targetAssetID)->getBytes(),
                     ]));
```
#### Pay BTC to API gateway with generated memo
```php
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
#### Read Bitcoin balance
```php
$mixinSdk = new MixinSDK(require './config.php');
$asset_info = $mixinSdk->Wallet()->readAsset(USDT_ASSET_ID);
print_r("USDT wallet balance is :".$asset_info["balance"]."\n");
```

## Solution Two: List your order on Ocean.One exchange
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
