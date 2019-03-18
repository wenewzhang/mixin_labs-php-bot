# PHP Bitcoin tutorial baseado na Mixin Network III: Criar uma carteira Bitcoin, ler o balanço e enviar Bitcoin
![](https://github.com/wenewzhang/mixin_labs-php-bot/raw/master/Bitcoin_php.jpg)

Nós criamos um bot para [ecoar mensagem](https://github.com/wenewzhang/mixin_labs-php-bot/blob/master/README.md) e [ecoar Bitcoin](https://github.com/wenewzhang/mixin_labs-php-bot/blob/master/README2.md).

### O que você aprenderá deste capítulo
1. Como criar uma carteira Bitcoin
2. Como ler o saldo de Bitcoin
3. Como mandar Bitcoin com taxa de transação zero e confirmada em 1 segundo
4. Como enviar Bitcoin para outra carteira


Pré-requisito: Você deve ter uma conta no app Mixin Network. Criar uma conta pode ser feita por uma linha de código:

```php
$user_info = $mixinSdkBot->Network()->createUser("Tom cat");
```
A função em PHP SDK cria uma chave par RSA automaticamente, e então chama a Mixin Network para criar uma conta e retorna todas as informações da mesma.


```php
//Create User api include all account information
print_r($user_info);
print($user_info["pubKey"]);
$newConfig = array();
$newConfig["private_key"] = $user_info["priKey"];
$newConfig["pin_token"]   = $user_info["pin_token"];
$newConfig["session_id"]  = $user_info["session_id"];
$newConfig["client_id"]   = $user_info["user_id"];
```

O resultado do createUser é:
```php
Array
(
    [type] => user
    [user_id] => de06f952-6ec7-3789-8467-9aa79869a6ef
    [identity_number] => 0
    [full_name] => Tom cat
    [avatar_url] =>
    [relationship] =>
    [mute_until] => 0001-01-01T00:00:00Z
    [created_at] => 2019-02-20T12:29:29.86008273Z
    [is_verified] =>
    [session_id] => bc9293e5-ed9a-48da-99f9-915f561a1c60
    [phone] =>
    [pin_token] => TIPyCtRTTYOg2sr+lu0z2D3xS8SOtQAy0ZDnacRrn6u2ytutZinzeEpRTD9N1+DS/T1zJ8VoX4ED19nhF5SApjqjUaRjKI5lga4rQGcePjCvM0D89FdpmKJzNMLjzV2DglKFMPbnJTu1btfILc0XWiSNEiiFr2mHuLI7bYuQzWI=
    [invitation_code] =>
    [code_id] =>
    [code_url] => https://mixin.one/codes/
    [has_pin] =>
    [receive_message_source] => EVERYBODY
    [accept_conversation_source] => EVERYBODY
    [priKey] => -----BEGIN PRIVATE KEY-----
MIICdgIBADANBgkqhkiG9w0BAQEFAASCAmAwggJcAgEAAoGBALh0dSy2GcKek/Jp
4lTMZxJ30AWP+inZ4c+FG+3ch3fenmXysCyM56hgvVZwh4RrRpvVjRt/NNE3k2Wg
N9LNZqWXCmo4ae/hJjpwuj/EVR/1/HSebF9hcvMoTre8D0iLlk+rf1tgr/ZHmIoa
8ef45xMBDargfsF4b5k7kUavU9/xAgMBAAECgYB1ShBMOwsMVxvKdIvn0gXkl20e
bFvtis9szr5gtO8rSNK+DuD5oyuXRNSAh5OUn0ZJxzQv/OZP9x/x6jw0/kk7Aj6c
jjN3beC7UoayDYms4yNFoWNPqZEXkQ0b2tRsF3mdNj6LVm6Gq7FPDD1TYJ4GR4eO
cWHCkZWym26HbZ30AQJBAPNFeZ7nd9wQIzu0wN9isrZebnCko3yax64MDsUAsrmP
B1wdHkdX0tJpCldighYD10Cyi+nSz3ODmmbPbLu8AjECQQDCGyi0lpCoV+skLVR0
4weU99Msz1neqOw1khQCJLzUW8UdDhsVwfCdzCeuZrCz+gl/aZaJ6d+6rNTMp1hL
ionBAkBEs34hTiUfVL9egTFm5KyrrAdscFJrQhraIDWblRLkLGxbqy194GN9YIS3
IO6z4OnNL58rrYlAig30sud2LSZBAkEAjuNXT7kWvBYcbwE/jtwhlLPqrK3nRlWr
rLPgLsPEjb8Ql5busVGXQ1IqU+QcaCDEJRshSlzz6YOZEx6NjO5rAQJAejvW3DmT
RjUSDJD8hGr9eCpKQTBDXyUEvyLIMCuRmm9Cbz0HRl4aVXOVblVWoJ6YsGvbCkSl
LQCrPL2T58JTkg==
-----END PRIVATE KEY-----

    [pubKey] => -----BEGIN PUBLIC KEY-----
MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQC4dHUsthnCnpPyaeJUzGcSd9AF
j/op2eHPhRvt3Id33p5l8rAsjOeoYL1WcIeEa0ab1Y0bfzTRN5NloDfSzWallwpq
OGnv4SY6cLo/xFUf9fx0nmxfYXLzKE63vA9Ii5ZPq39bYK/2R5iKGvHn+OcTAQ2q
4H7BeG+ZO5FGr1Pf8QIDAQAB
-----END PUBLIC KEY-----
)
```

Agora você precisa manter as informações da conta cuidadosamente. Essas informações são exigidas para ler o balanço e outros conteúdos da conta
### Criar uma carteira Bitcoin para a conta na Mixin Network
A carteira de Bitcoin não é gerada automaticamente ao mesmo tempo quando nós criamos a conta na Mixin Network. Leia o Ativo Bitcoin uma vez para gerar a Carteira Bitcoin.
```php
$asset_infoNew = $mixinSdkNew->Wallet()->readAsset("c6d0c728-2624-429b-8e0d-d9d19b6592fa");
echo "BitCoin wallet address is :".$asset_infoNew["public_key"];
```
Você pode achar a informação sobre o ativo Bitcoin na conta. A public key é o endereço de depósito de  Bitcoin. A resposta completa de Ler o ativo Bitcoin é:
```php
Array
(
    [type] => asset
    [asset_id] => c6d0c728-2624-429b-8e0d-d9d19b6592fa
    [chain_id] => c6d0c728-2624-429b-8e0d-d9d19b6592fa
    [symbol] => BTC
    [name] => Bitcoin
    [icon_url] => https://images.mixin.one/HvYGJsV5TGeZ-X9Ek3FEQohQZ3fE9LBEBGcOcn4c4BNHovP4fW4YB97Dg5LcXoQ1hUjMEgjbl1DPlKg1TW7kK6XP=s128
    [balance] => 0
    [public_key] => 195p8R8Y15uzDGMrdVkELVUW2444psqiSq
    [account_name] =>
    [account_tag] =>
    [price_btc] => 1
    [price_usd] => 3928.11498197
    [change_btc] => 0
    [change_usd] => -0.006841408545228452
    [asset_key] => c6d0c728-2624-429b-8e0d-d9d19b6592fa
    [confirmations] => 12
    [capitalization] => 0
)
```


A API fornece muitas informações sobre o ativo Bitcoin.
* Endereço de depósito:[public_key]
* Logo: [icon_url]
* Nome do ativo:[name]
* Ativo uuid na Mixin network: [asset_key]
* Preço em USD do Coinmarketcap.com: [price_usd]
* Últimos blocos confirmados antes do depósito ser aceito pela Mixin Network:[confirmations]


### Chave privada?
Onde está a chave privada do Bitcoin? A chave privada é protegida por multi-assinaturas dentro da Mixin Network e então é invisível para o usuário. O ativo Bitcoin pode ser sacado somente para outro endereço quando o usuário fornece a correta assinatura RSA private key, código PIN and Session key.

### Não apenas Bitcoin, mas também Ethereum, EOS
A conta não contem somente uma carteira Bitcoin, mas também possui carteira para Ethereum, EOS, etc. [Lista](https://mixin.one/network/chains) completa de blockchains suportadas. Todos os tokens ERC20 e EOS são suportados pela conta.

Criar a carteira de outro ativo é au mesma coisa do que criar uma de Bitcoin, apenas leia o ativo.
#### Criptomoedas suportadas pela Mixin Network (19/02/2019)

|crypto |uuid in Mixin Network
|---|---
|EOS|6cfe566e-4aad-470b-8c9a-2fd35b49c68d
|CNB|965e5c6e-434c-3fa9-b780-c50f43cd955c
|BTC|c6d0c728-2624-429b-8e0d-d9d19b6592fa
|ETC|2204c1ee-0ea2-4add-bb9a-b3719cfff93a
|XRP|23dfb5a5-5d7b-48b6-905f-3970e3176e27
|XEM|27921032-f73e-434e-955f-43d55672ee31
|ETH|43d61dcd-e413-450d-80b8-101d5e903357
|DASH|6472e7e3-75fd-48b6-b1dc-28d294ee1476
|DOGE|6770a1e5-6086-44d5-b60f-545f9d9e8ffd
|LTC|76c802a2-7c88-447f-a93e-c29c9e5dd9c8
|SC|990c4c29-57e9-48f6-9819-7d986ea44985
|ZEN|a2c5d22b-62a2-4c13-b3f0-013290dbac60
|ZEC|c996abc9-d94e-4494-b1cf-2a3fd3ac5714
|BCH|fd11b6e3-0b87-41f1-a41f-f0e9b49e5bf0

Se você ler o endereço de depósito EOS, o endereço de depósito é composto de duas partes: account_name e account tag. Quando você transfere token EOS para sua conta na Mixin Network, você deve preencher ambos account name e memo. O conteúdo memo é o valor de 'account_tag'.
O resultado de ler o ativo EOS é:
```php
Array
(
    [type] => asset
    [asset_id] => 6cfe566e-4aad-470b-8c9a-2fd35b49c68d
    [chain_id] => 6cfe566e-4aad-470b-8c9a-2fd35b49c68d
    [symbol] => EOS
    [name] => EOS
    [icon_url] => https://images.mixin.one/a5dtG-IAg2IO0Zm4HxqJoQjfz-5nf1HWZ0teCyOnReMd3pmB8oEdSAXWvFHt2AJkJj5YgfyceTACjGmXnI-VyRo=s128
    [balance] => 0
    [public_key] =>
    [account_name] => eoswithmixin
    [account_tag] => 0aa2b00fad2c69059ca1b50de2b45569
    [price_btc] => 0.00097367
    [price_usd] => 3.87734515
    [change_btc] => 0.05950956117519646
    [change_usd] => 0.07238079041492786
    [asset_key] => eosio.token:EOS
    [confirmations] => 64
    [capitalization] => 0
)
```

### Depositar Bitcoin e ler o balanço
Agora você pode depositar Bitcoin no endereço de depósito.

Isso talvez seja muito caro para este tutorial. Existe uma solução rápida e gratuita para depositar Bitcoin: adicione o endereço em seu endereço de saque na sua conta Mixin Messenger e saque uma pequena quantidade de Bitcoin de sua conta para o endereço. É gratuito e confirmado instantaneamente porque ambos estão na Mixin Network.

Agora você pode ler o balanço de Bitcoin da conta.
```php
$btc = $mixinSdkNew->Wallet()->readAsset("c6d0c728-2624-429b-8e0d-d9d19b6592fa");
print_r($btc);
```
### Envie Bitcoin dentro da Mixin Network para aproveitar confirmação instantânea e ZERO taxa de transação
Qualquer transação executada entre contas Mixin Network é gratuita e confirmada em 1 segundo.

Pré-requisito: Um PIN foi criado para a conta

Um PIN é exigido para enviar qualquer ativo Mixin Network. Vamos criar um PIN para a conta se estiver faltando.
```php
//Create a PIN.
$pinInfo = $mixinSdkNew->Pin()->updatePin('',PIN);
print_r($pinInfo);
```
#### Enviar Bitcoin para outra conta na Mixin Network
Nós podemos mandar Bitcoin para nosso bot através do Mixin Messenger, e então transferir Bitcoin do bot para um novo usuário.

```php
$mixinSdkBot = new MixinSDK(require './config.php');
//$user_info["user_id"] generated by create user;
$trans_info = $mixinSdkBot->Wallet()->transfer(BTC_ASSET_ID,$user_info["user_id"],
                                         $mixinSdkBot->getConfig()['default']['pin'],AMOUNT);
```

Leia o balanço de Bitcoin do bot para confirmar a transação.
Cuidado: **$mixinSdkNew** é para o Novo Usuário!
```php
$btc = $mixinSdkNew->Wallet()->readAsset("c6d0c728-2624-429b-8e0d-d9d19b6592fa");
print_r($btc);
```
### Enviar Bitcoin para outra exchange de Bitcoin ou carteira
Se você quer enviar Bitcoin para outra exchange ou carteira, você precisa saber o endereço de depósito destino, então adicione o endereço na lista de endereço de saque na conta da Mixin Network.

Pré-requisito: O endereço de saque é adicionado e sabe a taxa de saque de Bitcoin

#### Adicionar endereço de destino à lista de endereço de saque
Chame createAddress, a ID do endereço será retornada em resultado da API e é exigida em breve.
```php
$btcInfo = $mixinSdkNew->Wallet()->createAddress("c6d0c728-2624-429b-8e0d-d9d19b6592fa",
                                                    "14T129GTbXXPGXXvZzVaNLRFPeHXD1C25C",
                                                    $mixinSdkNew->getConfig()['default']['pin'],
                                                    "BTC withdral",false);
```
O **14T129GTbXXPGXXvZzVaNLRFPeHXD1C25C** é um endereço de carteira Bitcoin, a saída segue abaixo, a taxa é 0.0025738 BTC, O resultado da API contém o ID do endereço de saque.                                           
```php
Array
(
    [type] => address
    [address_id] => 345855b5-56a5-4f3b-ba9e-d99601ef86c1
    [asset_id] => c6d0c728-2624-429b-8e0d-d9d19b6592fa
    [public_key] => 14T129GTbXXPGXXvZzVaNLRFPeHXD1C25C
    [label] => BTC withdral
    [account_name] =>
    [account_tag] =>
    [fee] => 0.0025738
    [reserve] => 0
    [dust] => 0.0001
    [updated_at] => 2019-02-20T01:47:56.44067294Z
)
```


#### Leia a taxa de saque a qualquer hora
```php
$wdInfo = $mixinSdkBot->Wallet()->readAddress($btcInfo["address_id"]);
```

#### Enviar Bitcoin para o endereço de destino
Envie a solicitação de saque para a Mixin Network, o $btcInfo["address_id"] é o endereço de id retornado pelo createAddress
```php
$wdInfo = $mixinSdkBot->Wallet()->withdrawal($btcInfo["address_id"],
                            "0.01",
                            $mixinSdkBot->getConfig()['default']['pin'],
                            "BTC withdral");
```
#### Confirme a transação no explorer da blockchain

## Exemplo completo
```php
<?php
require __DIR__ . '/vendor/autoload.php';
use ExinOne\MixinSDK\MixinSDK;
$mixinSdkBot = new MixinSDK(require './config.php');

const PIN             = "945689";
const MASTER_ID       = "37222956";
const BTC_ASSET_ID    = "c6d0c728-2624-429b-8e0d-d9d19b6592fa";
const EOS_ASSET_ID    = "6cfe566e-4aad-470b-8c9a-2fd35b49c68d";
const BTC_WALLET_ADDR = "14T129GTbXXPGXXvZzVaNLRFPeHXD1C25C";
const AMOUNT          = "0.001";
// Mixin Network support cryptocurrencies (2019-02-19)
// |EOS|6cfe566e-4aad-470b-8c9a-2fd35b49c68d
// |CNB|965e5c6e-434c-3fa9-b780-c50f43cd955c
// |BTC|c6d0c728-2624-429b-8e0d-d9d19b6592fa
// |ETC|2204c1ee-0ea2-4add-bb9a-b3719cfff93a
// |XRP|23dfb5a5-5d7b-48b6-905f-3970e3176e27
// |XEM|27921032-f73e-434e-955f-43d55672ee31
// |ETH|43d61dcd-e413-450d-80b8-101d5e903357
// |DASH|6472e7e3-75fd-48b6-b1dc-28d294ee1476
// |DOGE|6770a1e5-6086-44d5-b60f-545f9d9e8ffd
// |LTC|76c802a2-7c88-447f-a93e-c29c9e5dd9c8
// |SC|990c4c29-57e9-48f6-9819-7d986ea44985
// |ZEN|a2c5d22b-62a2-4c13-b3f0-013290dbac60
// |ZEC|c996abc9-d94e-4494-b1cf-2a3fd3ac5714
// |BCH|fd11b6e3-0b87-41f1-a41f-f0e9b49e5bf0

$msg  = "1: Create user and update PIN\n2: Read Bitcoin balance \n3: Read Bitcoin Address\n4: Read EOS balance\n";
$msg .= "5: Read EOS address\n6: Transfer Bitcoin from bot to new user\n7: Transfer Bitcoin from new user to Master\n";
$msg .= "8: Withdraw bot's Bitcoin\n";
$msg .= "9: Exit \nMake your choose:";
while (true) {
  echo $msg;
  $line = readline("");
  if ($line != '9') print("run...\n");
  if ($line == '1') {
    $user_info = $mixinSdkBot->Network()->createUser("Tom cat");
    print_r($user_info);
    print($user_info["pubKey"]);

    $newConfig = array();
    $newConfig["private_key"] = $user_info["priKey"];
    $newConfig["pin_token"]   = $user_info["pin_token"];
    $newConfig["session_id"]  = $user_info["session_id"];
    $newConfig["client_id"]   = $user_info["user_id"];
    $newConfig["pin"]         = PIN;
    $mixinSdkNew = new MixinSDK($newConfig);

    $pinInfo = $mixinSdkNew->Pin()->updatePin('',PIN);
    print_r($pinInfo);
    $csvary = array($newConfig);
    $fp = fopen('new_users.csv', 'a');
    foreach ($csvary as $fields) {
        fputcsv($fp, $fields);
    }
    fclose($fp);
  }
  if ($line == '2') {
    if (($handle = fopen("new_users.csv", "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
      $mixinSdkNew = new MixinSDK(GenerateConfigByCSV($data));
      $asset_info = $mixinSdkNew->Wallet()->readAsset(BTC_ASSET_ID);
      print_r("Bitcoin wallet balance is :".$asset_info["balance"]."\n");
    }
      fclose($handle);
    } else print("Create user first\n");
  }
  if ($line == '3') {
    if (($handle = fopen("new_users.csv", "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
      $mixinSdkNew = new MixinSDK(GenerateConfigByCSV($data));
      $asset_info = $mixinSdkNew->Wallet()->readAsset(BTC_ASSET_ID);
      print_r("Bitcoin wallet address is :".$asset_info["public_key"]."\n");
    }
      fclose($handle);
    } else print("Create user first\n");
  }
  if ($line == '4') {
    if (($handle = fopen("new_users.csv", "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
      $mixinSdkNew = new MixinSDK(GenerateConfigByCSV($data));
      $asset_info = $mixinSdkNew->Wallet()->readAsset(EOS_ASSET_ID);
      print_r("EOS wallet balance is :".$asset_info["balance"]."\n");
    }
      fclose($handle);
    } else print("Create user first\n");
  }
  if ($line == '5') {
    if (($handle = fopen("new_users.csv", "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
      $mixinSdkNew = new MixinSDK(GenerateConfigByCSV($data));
      $asset_info = $mixinSdkNew->Wallet()->readAsset(EOS_ASSET_ID);
      print_r($asset_info);
      print_r("EOS wallet address is :".$asset_info["account_name"]."\n");
      print_r($asset_info["account_tag"]."\n");
    }
      fclose($handle);
    } else print("Create user first\n");

  }
  if ($line == '6') {
    if (($handle = fopen("new_users.csv", "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
      $new_user_id = $data[3];
      $trans_info = $mixinSdkBot->Wallet()->transfer(BTC_ASSET_ID,$new_user_id,
                                               $mixinSdkBot->getConfig()['default']['pin'],AMOUNT);
      print_r($trans_info);
    }
      fclose($handle);
    } else print("Create user first\n");
  }
  if ($line == '7') {
    $userInfo = $mixinSdkBot->Network()->readUser(MASTER_ID);
    if (isset($userInfo["user_id"])) {
      if (($handle = fopen("new_users.csv", "r")) !== FALSE) {
      while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
          $mixinSdkNew = new MixinSDK(GenerateConfigByCSV($data));
          $asset_info = $mixinSdkNew->Wallet()->readAsset(BTC_ASSET_ID);
          if ( (float) $asset_info["balance"] > 0 ) {
            $trans_info = $mixinSdkNew->Wallet()->transfer(BTC_ASSET_ID,$userInfo["user_id"],
                                                     $mixinSdkNew->getConfig()['default']['pin'],$asset_info["balance"]);
            print_r($trans_info);
          } else print($data[3] . " has no coins!\n");
      }
        fclose($handle);
      } else print("Create user first\n");
    } else print("Can not find this user id by Mixin ID!");
  }
  if ($line == '8') {
    $btcInfo = $mixinSdkBot->Wallet()->createAddress(BTC_ASSET_ID,
                                              BTC_WALLET_ADDR,
                                              $mixinSdkBot->getConfig()['default']['pin'],
                                              "BTC withdral",false);
    print("Bitcoin winthdrawal fee is:".$btcInfo["fee"]."\n");
    $wdInfo = $mixinSdkBot->Wallet()->withdrawal($btc["address_id"],
                                AMOUNT,
                                $mixinSdkBot->getConfig()['default']['pin'],
                                "BTC withdral");
    // $wdInfo = $mixinSdkBot->Wallet()->readAddress($btcInfo["address_id"]);
    print_r($wdInfo);
  }
  if ($line == '9') {
    exit();
  }
}

function GenerateConfigByCSV($data) :array {
  print("client id is:".$data[3]."\n");
  $newConfig = array();
  $newConfig["private_key"] = $data[0];
  $newConfig["pin_token"]   = $data[1];
  $newConfig["session_id"]  = $data[2];
  $newConfig["client_id"]   = $data[3];
  $newConfig["pin"]         = $data[4];
  return $newConfig;
}


```
[Código fonte completo](https://github.com/wenewzhang/mixin_labs-php-bot/blob/master/call_apis.php)
