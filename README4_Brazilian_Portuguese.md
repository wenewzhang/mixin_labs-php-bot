
# Como negociar bitcoin através da linguagem PHP
![](https://github.com/wenewzhang/mixin_labs-php-bot/raw/master/Bitcoin_php.jpg)

## Solução um: pague para a API ExinCore
[Exincore](https://github.com/exinone/exincore) fornece uma API de negociação comercial na Mixin Network.

Você paga USDT para a ExinCore, a ExinCore transfere Bitcoin para você na hora com uma taxa bem baixa e um preço justo. Toda transação é anônima para o público mas ainda pode ser verificada no explorer da blockchain. Apenas você e ExinCore sabem os detalhes.

A ExinCore não sabe quem você é porque a ExinCore sabe apenas seu uuid de cliente.

### Pré-requisito
Você deve ter criado um bot baseado na Mixin Network. Crie um lendo [PHP Bitcoin tutorial](https://github.com/wenewzhang/mixin_labs-php-bot).

#### Instale os pacotes exigidos
Como você sabe, nós apresentamos a você o mixin-sdk-php no [cápitulo 1](https://github.com/wenewzhang/mixin_labs-php-bot/blob/master/README.md), supondo que foi instalado antes, vamos instalar o **uuid, msgpack** aqui.
```bash
  composer require ramsey/uuid
  composer require rybakit/msgpack
```
#### Deposite USDT ou Bitcoin na sua conta Mixin Network e leia o saldo
ExinCore pode cambiar entre Bitcoin, USDT, EOS, Eth etc. Aqui te mostra como cambiar entre USDT e Bitcoin,
Verifique o saldo e endereço da carteira antes de fazer a ordem.

- Verifique o endereço e o saldo, lembre que é um endereço de carteira Bitcoin.
- Deposite Bitcoin neste endereço de carteira Bitcoin.
- Verifique o saldo de Bitcoin após 100 minutos mais tarde.

**A propósito, o endereço Bitcoin & USDT são os mesmos.**

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
#### Leia o preço de mercado
Como verificar o preço da moeda? Você precisa entender qual é a moeda base. Se você quer comprar Bitcoin e vender USDT, o USDT é a moeda base. Se você quer comprar USDT e vender Bitcoin, o Bitcoin é a moeda base.
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

#### Crie um memorando para preparar a ordem
O capítulo dois: [Ecoar Bitcoin](https://github.com/wenewzhang/mixin_labs-php-bot/blob/master/README2.md) apresenta transferência de moedas. Mas você precisa deixar a ExinCore saber qual moeda você quer comprar. Apenas escreva seu ativo alvo no memorando.
```php
$memo = base64_encode(MessagePack::pack([
                     'A' => Uuid::fromString($_targetAssetID)->getBytes(),
                     ]));
```
#### Pague BTC para a gateway do API como o memorando gerado
Transferir Bitcoin(BTC_ASSET_ID) para ExinCore(EXIN_BOT), coloque seu uuid do ativo alvo no memorando, de outra forma, ExinCore reembolsará a moeda imediatamente!
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
A ExinCore deve transferir a moeda alvo para o seu bot, enquanto isso, coloque as informações de taxa, id da ordem, preço e etc. no memorando, descompacte os dados igual abaixo.
- **readUserSnapshots** lê snapshots do usuário.
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

Se sua troca de moedas foi um sucesso, a saída do console mostrará o seguinte:
```bash
------------MEMO:-coin--exchange--------------
memo: hqFDzQPooVCnMzg3Mi45N6FGqTAuMDAwNzc0NqJGQcQQgVsLGidkNzaPqkLWlPpiCqFUoUahT8QQIbfeL6p5RVOcEP0mLb+t+g==
You Get Coins: 815b0b1a-2764-3736-8faa-42d694fa620a 0.3857508
Successful Exchange:
Fee asset ID: 815b0b1a-2764-3736-8faa-42d694fa620a fee is :0.0007746
Order ID: 21b7de2f-aa79-4553-9c10-fd262dbfadfa Price is :3872.97
--------------memo-record end---------------
```

#### Leia o saldo de Bitcoin
Verifique o saldo da carteira.
```php
$mixinSdk = new MixinSDK(require './config.php');
$asset_info = $mixinSdk->Wallet()->readAsset(USDT_ASSET_ID);
print_r("USDT wallet balance is :".$asset_info["balance"]."\n");
```
## Uso do código de fonte
Execute **php call_apis.php** para rodar isto.

- 1: Criar usuário e atualizar PIN
- 2: Ler o saldo e endereço de Bitcoin
- 3: Ler o saldo e o endereço USDT
- 4: Ler saldo EOS
- 5: Ler endereço EOS
- 6: Transferir Bitcoin do bot para um novo usuário
- 7: Transferir Bitcoin de um novo usuário para Master
- 8: Sacar Bitcoin do bot
- 9: Sacar EOS do bot
- qu: Ler o preço de mercado (USDT)
- qb: Ler o preço de mercado(BTC)
- b: Saldo do bot (USDT & BTC)
- s: Leitura instantânea
- tb: Transferir 0.0001 BTC comprar USDT
- tu: Transferir $1 USDT comprar BTC
- q: Sair

[Código fonte completo](https://github.com/wenewzhang/mixin_labs-php-bot/blob/master/call_apis.php)

## Solução dois: Listar sua ordem na exchange Ocean.One
