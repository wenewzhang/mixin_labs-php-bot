# 基于Mixin Network的PHP比特币开发教程
[Mixin Network](https://mixin.one) 是一个免费的 极速的端对端加密数字货币交易系统.
在本章中，你可以按教程在Mixin Messenger中创建一个bot来接收用户消息, 学到如何给机器人转**比特币** 或者 让机器人给你转**比特币**.

[Mixin network 资源汇总](https://github.com/awesome-mixin-network/index_of_Mixin_Network_resource)

## 课程简介
1. [创建一个接受消息的机器人](https://github.com/wenewzhang/mixin_labs-php-bot/blob/master/README-zhchs.md#创建一个接受消息的机器人)
2. [机器人接受比特币并立即退还用户](https://github.com/wenewzhang/mixin_labs-php-bot/blob/master/README2-zhchs.md)
3. [如何通过Mixin Network API创建新帐号](https://github.com/wenewzhang/mixin_labs-php-bot/blob/master/README3-zhchs.md)

## 创建一个接受消息的机器人
通过本教程，你将学会如何用PHP创建一个机器人APP,让它能接受消息.

### PHP 环境安装:
本教程的程序基于PHP 7开发，所以你需要先安装PHP7.2/PHP7.3与composer, 其中，composer是PHP的包管理系统!

#### on macOS
```bash
brew update
brew install php@7.2
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php -r "if (hash_file('sha384', 'composer-setup.php') === '48e3236262b34d30969dca3c37281b3b4bbe3221bda826ac6a9a62d6444cdb0dcd0615698a5cbe587c3f0fe57a54d8f5') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
//将PHP安装到/usr/local/opt/php@7.2/bin 目录，并取一个简单的各字：composer
php composer-setup.php --install-dir=/usr/local/opt/php@7.2/bin --filename=composer
php -r "unlink('composer-setup.php');"
```
如果之前你安装过php 7.1 或者更旧的php版本，执行**brew unlink php@7.1**删除这些链接，然后再执行**brew link php@7.3**将php 7.3链接到php.
```bash
wenewzha:mixin_labs-php-bot wenewzhang$ brew unlink php@7.1
Unlinking /usr/local/Cellar/php/7.1.23... 24 symlinks removed
wenewzha:mixin_labs-php-bot wenewzhang$ brew link php@7.3
Warning: php@7.3 is keg-only and must be linked with --force

If you need to have this software first in your PATH instead consider running:
  echo 'export PATH="/usr/local/opt/php@7.3/bin:$PATH"' >> ~/.bash_profile
  echo 'export PATH="/usr/local/opt/php@7.3/sbin:$PATH"' >> ~/.bash_profile
```
之后，根据你的系统实际情况，看看**php -v**能不能执行成功，如果不行，将默认的PATH设置并保存到.bash_profile.
```bash
echo 'export PATH="/usr/local/opt/php@7.3/bin:$PATH"' >> ~/.bash_profile
echo 'export PATH="/usr/local/opt/php@7.3/sbin:$PATH"' >> ~/.bash_profile
source ~/.bash_profile
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
php -r "if (hash_file('sha384', 'composer-setup.php') === '48e3236262b34d30969dca3c37281b3b4bbe3221bda826ac6a9a62d6444cdb0dcd0615698a5cbe587c3f0fe57a54d8f5') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
//将PHP安装到/usr/local/opt/php@7.2/bin 目录，并取一个简单的各字：composer
php composer-setup.php --install-dir=/usr/local/bin --filename=composer
php -r "unlink('composer-setup.php');"
```
最新的Composer在这儿[Composer](https://getcomposer.org/download/)
请确保PHP与composer安装在$PATH包含的目录之内，直接运行**php -v** 或 **composer -V** 就可以检查出来，如果提示如下，表示安装正确！
```bash
wenewzha:minecraft wenewzhang$ php -v
PHP 7.2.13 (cli) (built: Dec  7 2018 10:41:23) ( NTS )
Copyright (c) 1997-2018 The PHP Group
Zend Engine v3.2.0, Copyright (c) 1998-2018 Zend Technologies
    with Zend OPcache v7.2.13, Copyright (c) 1999-2018, by Zend Technologies
wenewzha:minecraft wenewzhang$ composer -V
Composer version 1.8.0 2018-12-03 10:31:16
```
如果提示command not found,表示bash没有在$PATH下找到php,请重复以上的安装步骤！
```bash
wenewzha:mixin_network-nodejs-bot2 wenewzhang$ php -v
-bash: php: command not found
```

### 创建你的项目
到你的工作文档中，创建一个目录，并取一个名字，比如:**mixin_labs-php-bot**
```bash
mkdir mixin_labs-php-bot
cd mixin_labs-php-bot
```
转到新创建的项目目录下, 执行 **composer init**, 依提示完成 composer.json的创建,
 ```bash
root@iZj6cbmqen2lqp7l48nfgkZ:~/mixin_labs-php-bot# composer init
  Welcome to the Composer config generator
This command will guide you through creating your composer.json config.
Package name (<vendor>/<name>) [user/mixin_labs-php-bot]:
Description []: PHP 7 bot for Mixin Messenger
Author [, n to skip]: JimmyZhang <this-a-email-address@nodomain.com>
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
            "email": "this-a-email-address@nodomain.com"
        }
    ],
    "require": {}
}
Do you confirm generation [yes]? yes
```
本教程引用了 [mixin-sdk-php](https://github.com/ExinOne/mixin-sdk-php) 与 [Ratchet pawl](https://github.com/ratchetphp/Pawl), **mixin-sdk-php** 是一个Mixin Network PHP SDK ,  **Ratchet pawl** 是一个WebSocket客户端.
打开composer.json, 在"require"增加两行引用:
```bash
"require": {
    "exinone/mixin-sdk-php": "^1.1",
    "ratchet/pawl": "^0.3.3",
},
```
保存composer.json后，执行 **composer install** 来下载：
```bash
composer install
```
下载完成后，目录下会出现一个vendor的子目录。
```bash
root@iZj6cbmqen2lqp7l48nfgkZ:~/mixin_labs-php-bot# ls
composer.json  composer.lock  vendor
```
 如果你是git克隆的源代码，直接执行 **composer install** 来下载依赖包。

### 创建第一个机器人APP
按下面的提示，到mixin.one创建一个APP[tutorial](https://mixin-network.gitbook.io/mixin-network/mixin-messenger-app/create-bot-account).

### 生成相应的参数
记下这些[生成的参数](https://mixin-network.gitbook.io/mixin-network/mixin-messenger-app/create-bot-account#generate-secure-parameter-for-your-app)
它们将用于config.php中.

![mixin_network-keys](https://github.com/wenewzhang/mixin_labs-php-bot/raw/master/mixin_network-keys.jpg)
在项目目录下，创建config.php,将生成的参数，替换成你的！
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
需要替换的参数包括： mixin_id, client_id, client_secret, and the pin, pin token, session_id, private key.

### 经典的Hello world
在项目目录下创建一个app.php文件，将下面的代码拷进去：
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
保存，并在终端里执行app.php
```bash
php app.php
```
如果一切正常，提示如下：
```bash
wenewzha:mixin_labs-php-bot wenewzhang$ php app.php
a1ce2967-a534-417d-bf12-c86571e4eefa{"id":"4454b6c5-4a89-440c-bd22-7a79cf4954ca","action":"LIST_PENDING_MESSAGES"}stdClass Object
(
    [id] => 4454b6c5-4a89-440c-bd22-7a79cf4954ca
    [action] => LIST_PENDING_MESSAGES
)
```
在手机安装 [Mixin Messenger](https://mixin.one/),增加机器人为好友,(比如这个机器人是7000101639) 然后发送消息给它,效果如下!

![mixin_messenger](https://github.com/wenewzhang/mixin_labs-php-bot/raw/master/helloworld.jpeg)


### 源代码解释
WebSocket是建立在TCP基础之上的全双工通讯方式，我们需要建立一个loop循环来维持通迅。
```php
$loop = \React\EventLoop\Factory::create();
$reactConnector = new \React\Socket\Connector($loop, [
    'timeout' => 15
]);
$connector = new \Ratchet\Client\Connector($loop,$reactConnector);
```
机器人APP通过Mixin Messenger服务器来接收用户发过来的消息，连接服务器，需要Token来验证用户的身份,
详细资料可参考如下链接:
[Token认证](https://developers.mixin.one/api/beta-mixin-message/authentication/), [接收服务器消息](https://mixin-network.gitbook.io/mixin-network/mixin-messenger-app/receive-asset-change-notification)

mixin-sdk-php 实现了令牌Token的生成(getToken), 调用代码如下：
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
连接到服务器，注意协议类型与Token:
```php
$connector('wss://blaze.mixin.one', ['protocol' => 'Mixin-Blaze-1'],[
                                    'Authorization' => 'Bearer '.$Token
                                      ])
```
向服务器发送"LIST_PENDING_MESSAGES",这样服务器才会将收到的消息发送给机器人APP.
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

增加侦听 **onMessage** 接收并分析消息:
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
Mixin Messenger支持的消息类型很多，具体可到下面链接查看:  [WebSocket消息类型](https://developers.mixin.one/api/beta-mixin-message/websocket-messages/).

每接收到一个消息，需要按消息编号(message_id)给服务器回复一个"已读"的消息,避免服务器在机器人重新登入后，再次发送处理过的消息！
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
现在你的机器人APP运行起来了，你打算如何改造你的机器人呢？

完整的代码[在这儿](https://github.com/wenewzhang/mixin_labs-php-bot/blob/master/helloworld.php)


### 下一课[机器人接收与处理加密货币](https://github.com/wenewzhang/mixin_labs-php-bot/blob/master/README2-zhchs.md)
