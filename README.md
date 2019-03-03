# PHP Bitcoin tutorial based on Mixin Network
![](https://github.com/wenewzhang/mixin_labs-php-bot/blob/master/Bitcoin_php.jpg)
A Mixin messenger bot will be created in this tutorial. The bot is powered by PHP and echo message and Bitcoin from user.

[Mixin network resource](https://github.com/awesome-mixin-network/index_of_Mixin_Network_resource)

## What you will learn from this tutorial
1. [How to create bot in Mixin messenger and reply message to user](https://github.com/wenewzhang/mixin_labs-php-bot#create-bot-and-receive-message-from-user)| [Chinese](https://github.com/wenewzhang/mixin_labs-php-bot/blob/master/README-zhchs.md) |[Indonesian](https://github.com/wenewzhang/mixin_labs-php-bot/blob/master/Indonesian/README_Indonesian.md)|
2. [How to receive Bitcoin and send Bitcoin in Mixin Messenger](https://github.com/wenewzhang/mixin_labs-php-bot/blob/master/README2.md)| [Chinese](https://github.com/wenewzhang/mixin_labs-php-bot/blob/master/README2-zhchs.md) 
3. [How to create a Bitcoin wallet based on Mixin Network API](https://github.com/wenewzhang/mixin_labs-php-bot/blob/master/README3.md)| [Chinese](https://github.com/wenewzhang/mixin_labs-php-bot/blob/master/README3-zhchs.md) 
## Create bot in Mixin messenger and reply message to user
### PHP environment setup:
This tutorial is written in PHP 7. So you need to install [PHP](http://php.net/) and [Composer](https://getcomposer.org/).

On macOS
```bash
brew update
brew install php@7.3
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php -r "if (hash_file('sha384', 'composer-setup.php') === '48e3236262b34d30969dca3c37281b3b4bbe3221bda826ac6a9a62d6444cdb0dcd0615698a5cbe587c3f0fe57a54d8f5') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
//install composer to /usr/local/opt/php@7.3/bin and give a brief name 'composer'
php composer-setup.php --install-dir=/usr/local/opt/php@7.3/bin --filename=composer
php -r "unlink('composer-setup.php');"
```
If you install a old php 7.1 before, execute **brew unlink php@7.1** to remove the symbol links, and then execute **brew link php@7.3** to link php 7.3 to **php** default
```bash
wenewzha:mixin_labs-php-bot wenewzhang$ brew unlink php@7.1
Unlinking /usr/local/Cellar/php/7.1.23... 24 symlinks removed
wenewzha:mixin_labs-php-bot wenewzhang$ brew link php@7.3
Warning: php@7.3 is keg-only and must be linked with --force

If you need to have this software first in your PATH instead consider running:
  echo 'export PATH="/usr/local/opt/php@7.3/bin:$PATH"' >> ~/.bash_profile
  echo 'export PATH="/usr/local/opt/php@7.3/sbin:$PATH"' >> ~/.bash_profile
```
After the php 7.3 installed and symlinks created, according your OS environment settings, if **php -v** prompt "command not found", issue **echo 'export PATH="/usr/local/opt/php@7.3/bin:$PATH"'** to bash_profile.
```bash
echo 'export PATH="/usr/local/opt/php@7.3/bin:$PATH"' >> ~/.bash_profile
echo 'export PATH="/usr/local/opt/php@7.3/sbin:$PATH"' >> ~/.bash_profile
source ~/.bash_profile
```

On Ubuntu
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
//install composer to /usr/local/bin and give a brief name 'composer'
php composer-setup.php --install-dir=/usr/local/bin --filename=composer
php -r "unlink('composer-setup.php');"
```
The latest composer can be download from [here](https://getcomposer.org/download/)
Make sure the the $PATH variable contains install directory, following command can be used to check the installation
```bash
wenewzha:minecraft wenewzhang$ php -v
PHP 7.2.13 (cli) (built: Dec  7 2018 10:41:23) ( NTS )
Copyright (c) 1997-2018 The PHP Group
Zend Engine v3.2.0, Copyright (c) 1998-2018 Zend Technologies
    with Zend OPcache v7.2.13, Copyright (c) 1999-2018, by Zend Technologies
wenewzha:minecraft wenewzhang$ composer -V
Composer version 1.8.0 2018-12-03 10:31:16
```
### Create the project
Go to your documents folder then create a directory, for example: **mixin_labs-php-bot**
```bash
mkdir mixin_labs-php-bot
mixin_labs-php-bot
```
Execute **composer init** in your project directory, follow the instruction to create the composer.json,
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
This tutorial requires two libraries.
* [mixin-sdk-php](https://github.com/ExinOne/mixin-sdk-php) is a PHP SDK for Mixin Network.
* [Ratchet pawl](https://github.com/ratchetphp/Pawl) is a asynchronous websocket client.

In composer.json file, add the two libraries in the "require" code block.
```bash
"require": {
    "exinone/mixin-sdk-php": "^1.1",
    "ratchet/pawl": "^0.3.3",
},
```
Save composer.json file and then execute **composer install** to download required libraries.
```bash
composer install
```
A vendor directory is created in the project directory after all libraries are downloaded.
```bash
root@iZj6cbmqen2lqp7l48nfgkZ:~/mixin_labs-php-bot# ls
composer.json  composer.lock  vendor
```
If you clone this repository from Github repo, you only need to execute **composer install** to download all libraries.

### Create your first app in Mixin Network developer dashboard
You need to create an app in dashboard. This [tutorial](https://mixin-network.gitbook.io/mixin-network/mixin-messenger-app/create-bot-account) can help you.

### Generate parameter of your app in dashboard
After app is created in dashboard, you still need to [generate parameter](https://mixin-network.gitbook.io/mixin-network/mixin-messenger-app/create-bot-account#generate-secure-parameter-for-your-app)
and write down required content, these content will be written into config.php file.

![mixin_network-keys](https://github.com/wenewzhang/mixin_labs-php-bot/blob/master/mixin_network-keys.jpg)
In project folder, create a file: config.php. Copy the following content into it.
> config.php
```php
<?php
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
Replace the value with **content generated in dashboard**.

### Hello world in PHP
Copy the following code into app.php, create app.php file if it is missing in your folder
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
Run the code
```bash
php app.php
```
The following content will be displayed in console.
```bash
wenewzha:mixin_labs-php-bot wenewzhang$ php helloworld.php
a1ce2967-a534-417d-bf12-c86571e4eefa{"id":"4454b6c5-4a89-440c-bd22-7a79cf4954ca","action":"LIST_PENDING_MESSAGES"}stdClass Object
(
    [id] => 4454b6c5-4a89-440c-bd22-7a79cf4954ca
    [action] => LIST_PENDING_MESSAGES
)
```
Add the bot(for example, this bot id is 7000101639) as your friend in [Mixin Messenger](https://mixin.one/messenger) and send your messages.
![mixin_messenger](https://github.com/wenewzhang/mixin_labs-php-bot/blob/master/helloworld.jpeg)


### Source code summary
The PHP code creates a websocket client.
```php
$loop = \React\EventLoop\Factory::create();
$reactConnector = new \React\Socket\Connector($loop, [
    'timeout' => 15
]);
$connector = new \Ratchet\Client\Connector($loop,$reactConnector);
```

The code generates a valid token and creates connection between the websocket and Mixin Messenger server. Messages will be pushed to websocket client.

[API of the operation](https://developers.mixin.one/api/beta-mixin-message/authentication/), [Guide of the operation](https://mixin-network.gitbook.io/mixin-network/mixin-messenger-app/receive-asset-change-notification)

The mixin-sdk-php implements the getToken function, call it and generate a token.
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
Connect to  Mixin messenger server with the correct token.
```php
$connector('wss://blaze.mixin.one', ['protocol' => 'Mixin-Blaze-1'],[
                                    'Authorization' => 'Bearer '.$Token
                                      ])
```
Send "LIST_PENDING_MESSAGES" to server to receive pending messages.
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
**onMessage** function will be called when message is pushed to websocket client.
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
Not only text, images and other type message will be pushed to your bot. You can find more [details](https://developers.mixin.one/api/beta-mixin-message/websocket-messages/) about Messenger message.

Send a READ operation message to the server let it knows this message has been read. The bot will receive the duplicated message when the bot connected to server again if bot don't send response.
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
### End
Now your bot worked, you can hack it.

Full code is [here](https://github.com/wenewzhang/mixin_labs-php-bot/blob/master/helloworld.php)
