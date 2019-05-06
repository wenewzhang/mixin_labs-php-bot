# Как торговать биткойнами с помощью PHP (часть IV)
![](https://github.com/wenewzhang/mixin_labs-php-bot/raw/master/Bitcoin_php.jpg)

## Решение номер один: оплата с использованием API ExinCore 
[Exincore](https://github.com/exinone/exincore) предоставляет API для коммерческой торговли на Mixin Network.

Вы вносите оплату в USDT на ExinCore, ExinCore конвертирует их по разумному курсу в биткойны и с очень низкой комиссией переводит вам. Каждая транзакция анонимна и может быть проверена на обозревателе блокчейна. Подробности известны только вам и ExinCore.

ExinCore не знает, кто вы, потому что использует только `UUID` клиента.

### Предварительное условие:
нужно создать бот на базе Mixin Network. Создать его можно, обратившись к [туториалу по PHP для работы с биткойном](https://github.com/wenewzhang/mixin_labs-php-bot).

#### Установите необходимые пакеты
Как вы уже знаете, мы представляем mixin-sdk-php в [части 1](https://github.com/wenewzhang/mixin_labs-php-bot/blob/master/README-russian.md). Будем считать, что он уже установлен. Теперь установим **uuid, msgpack**.
```bash
  composer require ramsey/uuid
  composer require rybakit/msgpack
```
#### Положите USDT или биткойн на депозит в своей учётной записи Mixin Network и проверьте баланс
ExinCore может производить обмен Bitcoin, USDT, EOS, Eth и т. д. Мы покажем, как производить обмен USDT на биткойн.
Проверьте баланс и адрес кошелька до отправки ордера.

- Проверьте адрес и баланс, запомните адрес биткойн-кошелька.
- Внесите биткойн на адрес этого биткойн-кошелька.
- Проверьте баланс биткойн-кошелька через 100 минут.

**Кстати, адреса биткойн и USDT не отличаются.**

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
#### Проверьте курс обмена
Как проверить курс обмена монет? Необходимо понимать, что такое базовая монета. Если вы покупаете биткойн, а продаёте USDT, то базовой монетой будет USDT. Если вы покупаете USDT, а продаёте биткойн, то базовой монетой будет биткойн.
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

#### Создайте `memo` для подготовки ордера
Во второй части « [Возврат биткойна](https://github.com/wenewzhang/mixin_labs-php-bot/blob/master/README2.md)» мы рассказывали, как отправить монеты. Но ExinCore должна знать, какие монеты вы хотите купить. Для этого нужно записать в `memo` нужный актив.
```php
$memo = base64_encode(MessagePack::pack([
                     'A' => Uuid::fromString($_targetAssetID)->getBytes(),
                     ]));
```
#### Создайте `memo` и заплатите биткойнами через шлюз API
Отправляйте биткойн(`BTC_ASSET_ID`) на ExinCore(`EXIN_BOT`) и укажите `UUID` нужного актива в `memo` — без него ExinCore сразу же вернет монету!
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
ExinCore должна отправить требуемую монету  вашему боту, указав в `memo` свою комиссию, идентификатор ордера, курс и т.д. (См. пример ниже). 
- **readUserSnapshots** — вывести краткую информацию о балансе пользователя 
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

Если обмен произведен успешно, консоль выведет следующее:
```bash
------------MEMO:-coin--exchange--------------
memo: hqFDzQPooVCnMzg3Mi45N6FGqTAuMDAwNzc0NqJGQcQQgVsLGidkNzaPqkLWlPpiCqFUoUahT8QQIbfeL6p5RVOcEP0mLb+t+g==
You Get Coins: 815b0b1a-2764-3736-8faa-42d694fa620a 0.3857508
Successful Exchange:
Fee asset ID: 815b0b1a-2764-3736-8faa-42d694fa620a fee is :0.0007746
Order ID: 21b7de2f-aa79-4553-9c10-fd262dbfadfa Price is :3872.97
--------------memo-record end---------------
```

#### Проверьте баланс кошелька
Проверьте и отразите баланс кошелька.
```php
$mixinSdk = new MixinSDK(require './config.php');
$asset_info = $mixinSdk->Wallet()->readAsset(USDT_ASSET_ID);
print_r("USDT wallet balance is :".$asset_info["balance"]."\n");
```
## Как пользоваться исходным кодом
Выполните команду **php call_apis.php** для запуска меню. 

- 1: Создать пользователя и обновить ПИН
- 2: Проверить баланс и адрес биткойн-кошелька
- 3: Проверить баланс и адрес USDT-кошелька
- 4: Проверить баланс EOS-кошелька
- 5: Проверить адрес EOS-кошелька
- 6: Перевести биткойн от бота новому пользователю
- 7: Перевести биткойн от нового пользователя на мастер-счёт
- 8: Вывести биткойн через бота
- 9: Вывести EOS через бота
- qu: Проверить курс (USDT)
- qb: Проверить курс (BTC)
- b: Проверить баланс бота (USDT и BTC)
- s: Вывести краткую информацию 
- tb: Перевести 0.0001 биткойн для покупки USDT
- tu: Перевести $1 USDT для покупки биткойна
- q: Выйти

[Исходный код полностью](https://github.com/wenewzhang/mixin_labs-php-bot/blob/master/call_apis.php)

## Решение номер два: Отправьте ордер на биржу Ocean.One
