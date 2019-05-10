# Как торговать биткойнами с помощью PHP
![](https://github.com/wenewzhang/mixin_labs-php-bot/raw/master/Bitcoin_php.jpg)

В [предыдущей, 4-й части](https://github.com/wenewzhang/mixin_labs-php-bot/blob/master/README4-russian.md) мы дали представление об ExinCore, где можно обменивать многие крипто-активы по курсу и получать свой актив через 1 секунду. Если вы хотите торговать активом по специальной цене или ExinCore его не поддерживает, то вам подойдет OceanOne.

## Решение №2: отправьте ордер на биржу Ocean.One
[Ocean.one](https://github.com/mixinNetwork/ocean.one) – это децентрализованная биржа, созданная в Mixin Network. Практически впервые децентрализованная биржа дает пользователям те же возможности, что и централизованная.

На OceanOne можно торговать любыми активами. Пополните счет на OceanOne активом, который хотите продать, напишите запрос в `memo` платежа, и OceanOne выставит ваш ордер на торги.  Актив поступает на ваш кошелек после успешной сделки.

* Без регистрации
* Без депозита
* Без оформления допуска к торгам

### Предварительное условие
Требуется создать бот на базе Mixin Network. Создать его можно, обратившись к [туториалу по PHP для работы с биткойном](https://github.com/wenewzhang/mixin_labs-php-bot/blob/master/README.md).

#### Установите необходимые пакеты
В [части 4](https://github.com/wenewzhang/mixin_labs-php-bot/blob/master/README4.md) мы представляем [**mixin-sdk-php**](https://packagist.org/packages/exinone/mixin-sdk-php). Будем считать, что он уже установлен.

#### Положите USDT или биткойн на депозит в своей учётной записи Mixin Network и проверьте баланс
Ocean.one может выполнить любой ордер. Мы продемонстрируем обмен USDT на биткойны. Проверьте баланс и адрес кошелька до отправки ордера.

- Проверьте адрес и баланс, найдите адрес биткойн-кошелька.
- Внесите биткойн на адрес этого биткойн-кошелька.
- Проверьте баланс биткойн-кошелька через 100 минут.

**Кстати, адреса биткойн и USDT не отличаются.**

```php
  const BTC_ASSET_ID     = "c6d0c728-2624-429b-8e0d-d9d19b6592fa";
  const EOS_ASSET_ID     = "6cfe566e-4aad-470b-8c9a-2fd35b49c68d";
  const USDT_ASSET_ID    = "815b0b1a-2764-3736-8faa-42d694fa620a";

  $mixinSdk_eachAccountInstance = new MixinSDK(GenerateConfigByCSV($data));
  $asset_info = $mixinSdk_eachAccountInstance->Wallet()->readAsset(BTC_ASSET_ID);
  print_r("Bitcoin wallet address is :".$asset_info["public_key"]."\n");
  print_r("Bitcoin wallet balance is :".$asset_info["balance"]."\n");
```

#### Проверьте стакан котировок на Ocean.one
Как проверить курс обмена монет? Необходимо понимать, что такое базовая монета. Если вы покупаете биткойн, а продаёте USDT, то базовой монетой будет USDT. Если вы покупаете USDT, а продаёте биткойн, то базовой монетой будет биткойн.


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

#### Создайте memo для подготовки ордера

Во второй части [Возврат биткойна](https://github.com/wenewzhang/mixin_labs-php-bot/blob/master/README2-russian.md) мы рассказывали, как отправить монеты. Но Ocean.one должна знать, какие монеты вы хотите купить.
- переменная `side` может быть `B` или `A`: `B` — если покупаете, `A` — если продаете;
- переменная `asset` — это `UUID` актива, который вы хотите купить;
- переменная `price` работает так: если `side` соответствует `B`, цена указывается в единицах покупаемого актива (`UUID`); если `side` соответствует `A`, цена указывается в единицах актива, переводимого на Ocean.one.

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

#### Создайте memo и заплатите биткойнами через OceanOne
Переведите биткойн(BTC_ASSET_ID) на Ocean.one(OCEANONE_BOT), укажите `uuid` нужного актива(USDT) в `memo`.

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

Чтобы купить XIN, выполните следующее:

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

Лог по успешно выполненному ордеру выглядит так:
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
    [price_btc] => 0,00019038
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
## Как отменить ордер
Чтобы отменить ордер, просто заплатите любую сумму любого актива на OceanOne и напишите `trace_id` в `memo`. Для Ocean.one это будет идентификатор ордера, например, **b12eed67-6cf4-481f-b25b-dd41f28e1984**,
С его помощью можно отменить ордер.

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
#### Проверьте баланс биткойна
Проверьте и отобразите баланс кошелька.
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

## Как пользоваться исходным кодом
Создайте и запустите его.

- запустите команду **php bitcoin_wallet.php**.

Команды для ведения торгов на OceanOne:

- o: Ocean.One Exchange
- q: Exit («Выйти»)

Make your choose(eg: q for Exit!):

- 1:  Fetch XIN/USDT orders
- s1: Sell XIN/USDT
- b1: Buy XIN/USDT
- 2:  Fetch ERC20(Benz)/USDT orders
- s2: Sell Benz/USDT
- b2: Buy Benz/USDT
- q:  Exit

[Исходный код полностью](https://github.com/wenewzhang/mixin_labs-php-bot/blob/master/bitcoin_wallet.php)

