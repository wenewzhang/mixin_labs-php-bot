# Mixin Messenger ロボットプログラム作成教程【PHP編】
[Mixin Network](https://mixin.one)は無料で、すばやい両設備間においての暗号化されたデジタル通貨（Digital currency）取引に関するシステムである。
本章において、教程通りMixin Messengerでbotを作成して、ユーザーからのメッセージを受信し、いかにロボットに**ビットコイン（Bitcoin）**で振替取引を行うかロボットに**ビットコイン（Bitcoin）**で振替取引を行ってもらうかなどを勉強する予定

[Mixin network リソース・サマリー](https://github.com/awesome-mixin-network/index_of_Mixin_Network_resource)

## カリキュラム案内
1. [メッセージを受信するロボットプログラムを作成](https://github.com/wenewzhang/mixin_labs-php-bot/blob/master/README-zhchs.md#メッセージを受信するロボットプログラムを作成)
2. [ロボットプログラムがビットコイン（Bitcoin）を受け取ってから、すぐユーザーに返却](https://github.com/wenewzhang/mixin_labs-php-bot/blob/master/README2-zhchs.md)

## メッセージを受信するロボットプログラムを作成
本教程を通して、如何にPHPを利用して、ロボットプログラム式APPを作成して、メッセージを受信することができるように。

### PHP インストール環境:
本教程でいうAPPがPHP7に基づいたので、事前にPHP7.2/PHP7.3とcomposerをインストールしなければならない。その中に、composerはPHPのパッケージ管理システムである!

#### on macOS
```bash
brew update
brew install php@7.2
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php -r "if (hash_file('sha384', 'composer-setup.php') === '93b54496392c062774670ac18b134c3b3a95e5a5e5c8f1a9f115f203b75bf9a129d5daa8ba6a13e2cc8a1da0806388a8') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
```
PHPを/usr/local/opt/php@7.2/binというディレクトリにインストールして、覚えやすい名前「composer」を付ける。
```bash
php composer-setup.php --install-dir=/usr/local/opt/php@7.2/bin --filename=composer
php -r "unlink('composer-setup.php');"
```
#### on Ubuntu
```bash
apt update
apt upgrade

//install php 7.2
apt-get install software-properties-common python-software-properties
add-apt-repository -y ppa:ondrej/php
apt-get update
apt-get install php7.2 php7.2-cli php7.2-common
//install composer
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php -r "if (hash_file('sha384', 'composer-setup.php') === '93b54496392c062774670ac18b134c3b3a95e5a5e5c8f1a9f115f203b75bf9a129d5daa8ba6a13e2cc8a1da0806388a8') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
```
PHPを/usr/local/opt/php@7.2/binというディレクトリにインストールして、覚えやすい名前「composer」を付ける。
```bash
php composer-setup.php --install-dir=/usr/local/bin --filename=composer
php -r "unlink('composer-setup.php');"
```
PHPとcomposerが$PATHの含むディレクトリにインストールされることを確保し、直接に**php -v** と **composer -V**を実行したら、チェックできる。以下通り表示したら、正しくインストールしたという意味。
```bash
wenewzha:minecraft wenewzhang$ php -v
PHP 7.2.13 (cli) (built: Dec  7 2018 10:41:23) ( NTS )
Copyright (c) 1997-2018 The PHP Group
Zend Engine v3.2.0, Copyright (c) 1998-2018 Zend Technologies
    with Zend OPcache v7.2.13, Copyright (c) 1999-2018, by Zend Technologies
wenewzha:minecraft wenewzhang$ composer -V
Composer version 1.8.0 2018-12-03 10:31:16
```
command not foundという表示があったら、bashは$PATHにおいてphpを発見できないということで、以上のインストール順序を繰り返しなさい。
```bash
wenewzha:mixin_network-nodejs-bot2 wenewzhang$ php -v
-bash: php: command not found
```

### 新しいプログラムを作成
フォルダーにディレクトリを作成して、名前を変更し、例えば**mixin_labs-php-bot**
```bash
mkdir mixin_labs-php-bot
cd mixin_labs-php-bot
```
作成したディレクトリにおいて、**composer init**を実行し、表示通りにcomposer.jsonを作成
 ```bash
root@iZj6cbmqen2lqp7l48nfgkZ:~/mixin_labs-php-bot# composer init
  Welcome to the Composer config generator
This command will guide you through creating your composer.json config.
Package name (<vendor>/<name>) [user/mixin_labs-php-bot]:
Description []: PHP 7 bot for Mixin Messenger
Author [, n to skip]: JimmyZhang <zhangjingping@mixin.one>
Minimum Stability []:
Package Type (e.g. library, project, metapackage, composer-plugin) []:
License []:
Define your dependencies.
Would you like to define your dependencies (require) interactively [yes]? no
Would you like to define your dev dependencies (require-dev) interactively [yes]? no
{
    "name": "user/mixin_labs-php-bot",
    "description": "PHP 7 bot for Mixin Messenger",
    "authors": [
        {
            "name": "JimmyZhang",
            "email": "zhangjingping@mixin.one"
        }
    ],
    "require": {}
}
Do you confirm generation [yes]? yes
```
本教程は[mixin-sdk-php](https://github.com/ExinOne/mixin-sdk-php) と [Ratchet pawl](https://github.com/ratchetphp/Pawl)を引用し、 **mixin-sdk-php**はMixin Network PHP SDKであり、 **Ratchet pawl**はWebSocketのクライアントである。
composer.jsonを実行し、"require"に二つのパッケージを追加:
```bash
"require": {
    "exinone/mixin-sdk-php": "^1.1",
    "ratchet/pawl": "^0.3.3",
},
```
composer.jsonを保存した後、**composer install**を実行してダウンロードする：
```bash
composer install
```
ダウンロード完成したら、ディレクトリにvendorというサブディレクトリができた。
```bash
root@iZj6cbmqen2lqp7l48nfgkZ:~/mixin_labs-php-bot# ls
composer.json  composer.lock  vendor
```
 gitが クロンしたソースコードの場合、直接的に **composer install**を実行して、依存関係のパッケージをダウンロードする。

### 初めてのプログラムAPPを作成
以下の通りにmixin.oneにAPPを作成して、[tutorial](https://mixin-network.gitbook.io/mixin-network/mixin-messenger-app/create-bot-account).

### それに応ずる変数を形成する
これらの[形成した変数]を記録して、(https://mixin-network.gitbook.io/mixin-network/mixin-messenger-app/create-bot-account#generate-secure-parameter-for-your-app)
config.phpに用いられる。

![mixin_network-keys](https://github.com/wenewzhang/mixin_labs-php-bot/blob/master/mixin_network-keys.jpg)
プログラムのディレクトリのしたに、config.php作成して、できた変数を替える。
> config.php
```php
return [
    'mixin_id'      => '7000101716',
    'client_id'     => 'a1ce2967-a534-417d-bf12-c86571e4eefa',
    'client_secret' => '7339866727d24eeec1c4ebb6c634fd25a7b9057ee6d5939cca9b6b9fc15f4d1f',
    'pin'           => '512772',
    'pin_token'     => 'abRdNq6soRALRG434IgR7WS/qP7LOcpfviqSfWfABdIKyZGLnWXFMrVCHpChIkBRGRAcsUguni0OoNsShddPVL3qoD5fxbF5dRUiRv14urH1Pmdl6zIZdCH159QMr5wLmmSHSGu2AihNkUHUo3bAJsrvOW0nke5y6R5YE/pNNfo=',
    'session_id'    => '51faabbf-48ff-4df2-898d-e9b318afae35',
    'private_key'   => <<<EOF
-----BEGIN RSA PRIVATE KEY-----
MIICXQIBAAKBgQCuKI65sJR9lQ1+kyKouWu3CpmkPdJKaFqKVMEWk9RRH1Wgju9n
z/y5MiBVZKUeeIYtwrCNKbbdkSPqMoj1kLh5XUk4HaV9DUt+s9USBHOgU8m5Pxov
Km+HQ+Pam62lHWn6ClYaNrDihpcdDg9i7Y8hY1cgKiUcdkFQmDQ9lz2VHwIDAQAB
AoGANHJSSOk8TnVMkwmMLnNoVL8EdcmIQpAac/4CB+KM1cEx8CAbSJAB82N9CTo9
32c8QRuYP2qIf0DuJ+EADbN/Wc3o9zRY3dkbnLo144g3YaKwDccSgUMux03ANHlP
MEPDxOUbxJTRPXmKgUZmGJrkAClGbr3pPyQDDHDWRQc9JUECQQDT7pUYcXtu+hSc
nAlZllzqkBG2gZrDYpPJ0JirpfNhaApBo+CGZYKQ1961o6+HcI9gZmZA8hPEhT6p
PlubjqxbAkEA0l89du8TIUGrY9/sxyfZif6aeEztXPwBHZ9r8dm0L8Mlu5zTrOX2
SUgu3znM6djmuRMS45iPHJbPkvw9ilaljQJBAJRN323Ec/D79ZKGKpDThN/rw0lo
tolFoU/Xtg5fycl/CbZXXFYQEOcU+Nc43Ss1HFAEOEf4Xtbluyyp42ce1wMCQElv
P4htyhK41rglaYTXr0NRYeCOkej8evM5PDgPU6u8hkZoZyeamo9YKCx6A8K5mUiP
lO9nyMUlC852SJEqz90CQQDBguGg5GGcfehpIZwERlMJgKGg1+13/9GfnEPdAW2v
px7DZoMG/pQ/SEa53tJHmGGD9+qyp93z/fEPXsD5RSwx
-----END RSA PRIVATE KEY-----
EOF
    ,  //import your private_key
];
```
替える必要がある変数は mixin_id, client_id, client_secret, and the pin, pin token, session_id, private key.などを含む。

### 有名なHello world
プログラムのディレクトリのしたに、app.phpというファイルを作成して、以下のコードをコピーして入れる。
```php
<?php

require __DIR__ . '/vendor/autoload.php';
use ExinOne\MixinSDK\Traits\MixinSDKTrait;
use ExinOne\MixinSDK\MixinSDK;
use Ramsey\Uuid\Uuid;
use Ratchet\RFC6455\Messaging\Frame;

$loop = \React\EventLoop\Factory::create();
$reactConnector = new \React\Socket\Connector($loop, [
    'timeout' => 15
]);
$connector = new \Ratchet\Client\Connector($loop,$reactConnector);
class callTraitClass {
  use MixinSDKTrait;
  public $config;
  public function __construct()
  {
      $config = require(__DIR__.'/config.php');
      $this->config        = $config;
  }
}
$callTrait = new callTraitClass();
$Token = $callTrait->getToken('GET', '/', '');
// $connector('ws://127.0.0.1:9000', ['protocol' => 'Mixin-Blaze-1'], ['Origin' => 'http://localhost',
$connector('wss://blaze.mixin.one', ['protocol' => 'Mixin-Blaze-1'],[
                                    'Authorization' => 'Bearer '.$Token
                                      ])
->then(function(Ratchet\Client\WebSocket $conn) {
    $conn->on('message', function(\Ratchet\RFC6455\Messaging\MessageInterface $msg) use ($conn) {
        $jsMsg = json_decode(gzdecode($msg));
        print_r($jsMsg);
        if ($jsMsg->action === 'CREATE_MESSAGE' and property_exists($jsMsg,'data')) {
          echo "\nNeed reply server a receipt!\n";
          $RspMsg = generateReceipt($jsMsg->data->message_id);
          $msg = new Frame(gzencode(json_encode($RspMsg)),true,Frame::OP_BINARY);
          $conn->send($msg);

          if ($jsMsg->data->category === 'PLAIN_TEXT') {
                $msgData = sendPlainText($jsMsg->data->conversation_id,
                                          base64_decode($jsMsg->data->data));
                $msg = new Frame(gzencode(json_encode($msgData)),true,Frame::OP_BINARY);
                $conn->send($msg);
          } //end of PLAIN_TEXT
        } //end of CREATE_MESSAGE

    });
    $conn->on('close', function($code = null, $reason = null) {
        echo "Connection closed ({$code} - {$reason})\n";
    });
/*                   start listen for the incoming message          */
    $message = [
        'id'     => Uuid::uuid4()->toString(),
        'action' => 'LIST_PENDING_MESSAGES',
    ];
    print_r(json_encode($message));
    $msg = new Frame(gzencode(json_encode($message)),true,Frame::OP_BINARY);
    $conn->send($msg);
    // $conn->send(gzencode($msg,1,FORCE_DEFLATE));
}, function(\Exception $e) use ($loop) {
    echo "Could not connect: {$e->getMessage()}\n";
    $loop->stop();
});

$loop->run();


function sendPlainText($conversation_id,$msgContent):Array {

   $msgParams = [
     'conversation_id' => $conversation_id,
     'category'        => 'PLAIN_TEXT',
     'status'          => 'SENT',
     'message_id'      => Uuid::uuid4()->toString(),
     'data'            => base64_encode($msgContent),//base64_encode("hello!"),
   ];
   $msgPayButton = [
     'id'     =>  Uuid::uuid4()->toString(),
     'action' =>  'CREATE_MESSAGE',
     'params' =>   $msgParams,
   ];
   return $msgPayButton;
}

function generateReceipt($msgID):Array {
  $IncomingMsg = ["message_id" => $msgID, "status" => "READ"];
  $RspMsg = ["id" => Uuid::uuid4()->toString(), "action" => "ACKNOWLEDGE_MESSAGE_RECEIPT",
              "params" => $IncomingMsg];
  return $RspMsg;
}

```
保存して、端末にapp.phpを実行する。
```bash
php app.php
```
すべてが正常であれば、以下の表示が出てくる。
```bash
wenewzha:mixin_labs-php-bot wenewzhang$ php app.php
a1ce2967-a534-417d-bf12-c86571e4eefa{"id":"4454b6c5-4a89-440c-bd22-7a79cf4954ca","action":"LIST_PENDING_MESSAGES"}stdClass Object
(
    [id] => 4454b6c5-4a89-440c-bd22-7a79cf4954ca
    [action] => LIST_PENDING_MESSAGES
)
```
スマホに [Mixin Messenger](https://mixin.one/)をインストールして、ロボットを友人として追加し、(例えばこの7000101639というロボット)メッセージを送ったら、効果が以下表示通りである。

![mixin_messenger](https://github.com/wenewzhang/mixin_labs-php-bot/blob/master/helloworld.jpeg)


### ソースコード説明
WebSocketがTCPに基づいた全二重通信(Full duplex Communication)であるので、loop循環を作成して通信を維持する。
```php
$loop = \React\EventLoop\Factory::create();
$reactConnector = new \React\Socket\Connector($loop, [
    'timeout' => 15
]);
$connector = new \Ratchet\Client\Connector($loop,$reactConnector);
```
プログラムAPPはMixin Messengerサーバーを通して、ユーザーからのメッセージを受け取る。サーバーにアクセスする場合、TokenでユーザーのIDを検証する。
詳しいことは下記のリンクを参照しなさい。
 [Token検証](https://developers.mixin.one/api/beta-mixin-message/authentication/), [サーバーからのメッセージを受け取る](https://mixin-network.gitbook.io/mixin-network/mixin-messenger-app/receive-asset-change-notification)

mixin-sdk-phpはTokenから(getToken)に形成することを実現し、使用したコードは以下通り：
```php
class callTraitClass {
  use MixinSDKTrait;
  public $config;
  public function __construct()
  {
      $config = require(__DIR__.'/config.php');
      $this->config        = $config;
  }
}
$callTrait = new callTraitClass();
$Token = $callTrait->getToken('GET', '/', '');
```
サーバーにアクセスするとき、協定種類とTokenに注意しなさい:
```php
$connector('wss://blaze.mixin.one', ['protocol' => 'Mixin-Blaze-1'],[
                                    'Authorization' => 'Bearer '.$Token
                                      ])
```
サーバーに"LIST_PENDING_MESSAGES"を発送してからこそ、サーバーが受け取ったメッセージをロボットAPPに発送する次第
```php
/*                   start listen for the incoming message          */
    $message = [
        'id'     => Uuid::uuid4()->toString(),
        'action' => 'LIST_PENDING_MESSAGES',
    ];
    print_r(json_encode($message));
    $msg = new Frame(gzencode(json_encode($message)),true,Frame::OP_BINARY);
    $conn->send($msg);
```

 **onMessage**を傍受するのを増加し、 受信してメッセージを分析する:
```php
->then(function(Ratchet\Client\WebSocket $conn) {
    $conn->on('message', function(\Ratchet\RFC6455\Messaging\MessageInterface $msg) use ($conn) {
        $jsMsg = json_decode(gzdecode($msg));
        print_r($jsMsg);
        if ($jsMsg->action === 'CREATE_MESSAGE' and property_exists($jsMsg,'data')) {
          echo "\nNeed reply server a receipt!\n";
          $RspMsg = generateReceipt($jsMsg->data->message_id);
          $msg = new Frame(gzencode(json_encode($RspMsg)),true,Frame::OP_BINARY);
          $conn->send($msg);

          if ($jsMsg->data->category === 'PLAIN_TEXT') {
                $msgData = sendPlainText($jsMsg->data->conversation_id,
                                          base64_decode($jsMsg->data->data));
                $msg = new Frame(gzencode(json_encode($msgData)),true,Frame::OP_BINARY);
                $conn->send($msg);
          } //end of PLAIN_TEXT
        } //end of CREATE_MESSAGE

    });
    $conn->on('close', function($code = null, $reason = null) {
        echo "Connection closed ({$code} - {$reason})\n";
    });                                      
```
Mixin Messengerが支持するメッセージの種類が多く、詳しいのは下記のリンクをアクセスしてみなさい。  [WebSocketメッセージの種類](https://developers.mixin.one/api/beta-mixin-message/websocket-messages/).

メッセージを受信するたびに、メッセージの番号(message_id)ごとにサーバーに「既読」というメッセージを返信して、サーバーがロボットプログラム再起動した後、また処理されたメッセージを再発送することを避ける。
```php
echo "\nNeed reply server a receipt!\n";
$RspMsg = generateReceipt($jsMsg->data->message_id);
$msg = new Frame(gzencode(json_encode($RspMsg)),true,Frame::OP_BINARY);
$conn->send($msg);

function generateReceipt($msgID):Array {
  $IncomingMsg = ["message_id" => $msgID, "status" => "READ"];
  $RspMsg = ["id" => Uuid::uuid4()->toString(), "action" => "ACKNOWLEDGE_MESSAGE_RECEIPT",
              "params" => $IncomingMsg];
  return $RspMsg;
}
```
### 完成
ただ今ロボットAPPが実行され、いかに自分のロボットを改造するのか？

すっかり整っているコードは[ここにある](https://github.com/wenewzhang/mixin_labs-php-bot/blob/master/helloworld.php)


### 次回は[ロボットが暗号化されたデジタル通貨の受信と処理について](https://github.com/wenewzhang/mixin_labs-php-bot/blob/master/README2-zhchs.md)
