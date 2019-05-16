# How to list any ERC20 token on decentralized market through PHP
![](https://github.com/wenewzhang/mixin_labs-php-bot/blob/master/php-eth.jpg)

OceanOne is introduced in [last chapter](https://github.com/wenewzhang/mixin_labs-php-bot/blob/master/README5.md), you can trade Bitcoin. All kinds of crypto asset on Mixin Network can be listed on OceanOne.All ERC20 token and EOS token can be listed. Following example will show you how to list a ERC20 token. You can list other token after read the tutorial.

There is a [ERC20 token](https://etherscan.io/token/0xc409b5696c5f9612e194a582e14c8cd41ecdbc67) called Benz. It is deposited into Mixin Network. You can search all transaction history from [Mixin Network browser](https://mixin.one/snapshots/2b9c216c-ef60-398d-a42a-eba1b298581d )

### Pre-request:
Deposit some coin to your wallet, and then use **getAssets** API fetch the asset UUID which Mixin Network gave it.

### Get the ERC-20 compliant coin UUID
The **getAssets** API return json data, for example:

- **asset_id** UUID of this coin
- **public_key** The wallet address for this coin
- **symbol**  Coin name, Eg: Benz.

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
The detail information of **getAssets** is output like below:
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
### Make the limit order
- **Limit Order to Buy**  at or below the market.
- **Limit Order to Sell**  at or above the market.

OceanOne support three base coin: USDT, XIN, BTC, that mean you can sell or buy it between USDT, XIN, BTC, so, you have there order: Benz/USDT, Benz/XIN, Benz/BTC, here show you how to make the sell order with USDT.

### Make the limit order to sell.

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

### Make the limit order to buy.
After the order commit, wait 1 minute to let the OceanOne exchange initialize it.
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
### Read orders book from Ocean.one
Now, check the orders-book.

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
### Command of make orders

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

[Full source code](https://github.com/wenewzhang/mixin_labs-php-bot/blob/master/bitcoin_wallet.php)
