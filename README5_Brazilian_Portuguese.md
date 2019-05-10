
# Como negociar bitcoin através de PHP
![](https://github.com/wenewzhang/mixin_labs-php-bot/raw/master/Bitcoin_php.jpg)

Exincore foi introduzido no [último capítulo](https://github.com/wenewzhang/mixin_labs-php-bot/blob/master/README4.md), você pode trocar muitos cripto ativos a preço de mercado e receber seu ativo em 1 segundo. Se você quer negociar ativos a um preço limitado, ou negociar ativos não suportados pela ExinCore no momento, OceanOne é a resposta.

## Solução Dois: Liste sua ordem na Ocean.One exchange
[Ocean.one](https://github.com/mixinNetwork/ocean.one) é uma exchange descentralizada construída na Mixin Network, é quase a primeira vez que uma exchange descentralizada obtêm a mesma experiência de usuário do que uma centralizada.

Você pode listar qualquer ativo na OceanOne. Pague o ativo que você quer vender para a conta OceanOne, escreva seu pedido no memo de pagamento, OceanOne listará sua ordem no mercado. Ela enviará o ativo para sua carteira após que sua ordem for correspondida.

* Não é necessário cadastro
* Não é necessário depósito.
* Sem processo de listagem.

### Pré-requisito:
Você deve ter criado um bot baseado na Mixin Network. Crie um lendo [PHP Bitcoin tutorial](https://github.com/wenewzhang/mixin_labs-php-bot/blob/master/README.md).

#### Instale pacotes necessários
[Capítulo 4](https://github.com/wenewzhang/mixin_labs-php-bot/blob/master/README4.md) apresenta [**mixin-sdk-php**](https://packagist.org/packages/exinone/mixin-sdk-php) para você, assumindo que foi instalado antes.

#### Deposite USDT ou Bitcoin na sua conta Mixin Network e leia o saldo
A Ocean.one pode corresponder qualquer ordem. Aqui nós cambiamos entre USDT e Bitcoin, Cheque o saldo da carteira & endereço antes de você fazer a ordem.

- Cheque o endereço e saldo, ache seu endereço de carteira Bitcoin.
- Deposite Bitcoin nesse endereço de carteira Bitcoin.
- Cheque o saldo de Bitcoin após 100 minutos ou mais.

**O endereço Omni USDT é o mesmo que o endereço Bitcoin**

```php
  const BTC_ASSET_ID     = "c6d0c728-2624-429b-8e0d-d9d19b6592fa";
  const EOS_ASSET_ID     = "6cfe566e-4aad-470b-8c9a-2fd35b49c68d";
  const USDT_ASSET_ID    = "815b0b1a-2764-3736-8faa-42d694fa620a";

  $mixinSdk_eachAccountInstance = new MixinSDK(GenerateConfigByCSV($data));
  $asset_info = $mixinSdk_eachAccountInstance->Wallet()->readAsset(BTC_ASSET_ID);
  print_r("Bitcoin wallet address is :".$asset_info["public_key"]."\n");
  print_r("Bitcoin wallet balance is :".$asset_info["balance"]."\n");
```

#### Leia o livro de ordens da Ocean.one
Como verificar o preço da moeda? Você precisa entender qual é a moeda base. Se você quer comprar Bitcoin e vender USDT, o USDT é a moeda base. Se você quer comprar USDT e vender Bitcoin, o Bitcoin é a moeda base.


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

#### Crie um memo para preparar a ordem
O capítulo dois: [Echo Bitcoin](https://github.com/wenewzhang/mixin_labs-php-bot/blob/master/README2.md) apresenta transferência de moedas. Mas você precisa deixar a Ocean.one saber qual moeda você deseja comprar.
- **side** "B" ou "A", "B" para compra, "A" para Venda.
- **asset** UUID do ativo que você quer comprar
- **price** Se Side é "B", Price é AssetUUID; se Side é "A", Price é o ativo que transfere para a Ocean.one.

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

#### Pague BTC para a OceanOne com o memo gerado
Transfira Bitcoin(BTC_ASSET_ID) para a Ocean.one(OCEANONE_BOT), coloque seu ativo alvo uuid(USDT) no memo.

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

Se você quer comprar XIN, chame-o como abaixo:

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

Uma ordem bem sucedida tem a saída conforme abaixo:
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
## Cancelar a ordem
Para cancelar uma ordem, apenas pague qualquer quantidade de qualquer ativo para a OceanOne, e escreva trace_id no memo. Ocean.one pega o trace_id como o id da ordem, por exemplo, **b12eed67-6cf4-481f-b25b-dd41f28e1984** é um id de ordem,
Nós podemos usá-lo para cancelar a ordem.

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
#### Leia o saldo de Bitcoin
Cheque o saldo da carteira
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

## Uso do código fonte
Construa e execute-o

- **php bitcoin_wallet.php** run it.

Comandos de negociação com a OceanOne:

- o: Ocean.One Exchange
- q: Sair

Faça sua escolha(eg: q para Sair!):

- 1:  Buscar ordens de XIN/USDT
- s1: Vender XIN/USDT
- b1: Comprar XIN/USDT
- 2:  Buscar ordens ERC20(Benz)/USDT
- s2: Vender Benz/USDT
- b2: Comprar Benz/USDT
- q:  Sair

[Código fonte completo](https://github.com/wenewzhang/mixin_labs-php-bot/blob/master/bitcoin_wallet.php)
