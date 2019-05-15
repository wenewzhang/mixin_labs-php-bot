 # Туториал по PHP для работы с биткойном через Mixin Network
![](https://github.com/wenewzhang/mixin_labs-php-bot/raw/master/Bitcoin_php.jpg)
С помощью данного туториала вы сможете создать бота для Mixin Messenger. Это будет echo-бот на PHP, который сможет принимать Bitcoin от пользователей.

[Каталог ресурсов по Mixin Network](https://github.com/awesome-mixin-network/index_of_Mixin_Network_resource)

## С помощью данного туториала вы научитесь
1. [Туториал по PHP для работы с биткойном через Mixin Network](https://github.com/wenewzhang/mixin_labs-php-bot/blob/master/README-russian.md)
2. [Туториал по PHP для работы с биткойном через Mixin Network, часть II: Получайте и отправляйте Bitcoin в Mixin Messenger](https://github.com/wenewzhang/mixin_labs-php-bot/blob/master/README2-russian.md)
3. [Туториал по PHP для работы с биткойном через Mixin Network, часть III: Создавайте биткойн-кошелёк, проверяйте баланс и отправляйте биткойны](https://github.com/wenewzhang/mixin_labs-php-bot/blob/master/README3-russian.md)
4. [Как торговать биткойнами с помощью PHP (часть IV)](https://github.com/wenewzhang/mixin_labs-php-bot/blob/master/README4-russian.md)
5. [Как торговать биткойнами с помощью PHP](https://github.com/wenewzhang/mixin_labs-php-bot/blob/master/README5-russian.md)

## Как создать бота для Mixin Messenger и сформировать ответные сообщения пользователям
### Настройка среды PHP
Данный туториал написан для `PHP 7.3`, поэтому нужно установить [PHP](http://php.net/) и менеджер зависимостей [Composer](https://getcomposer.org/).

Для `macOS`:
```bash
brew update
brew install php@7.3
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php -r "if (hash_file('sha384', 'composer-setup.php') === '48e3236262b34d30969dca3c37281b3b4bbe3221bda826ac6a9a62d6444cdb0dcd0615698a5cbe587c3f0fe57a54d8f5') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
//install composer to /usr/local/opt/php@7.3/bin and give a brief name 'composer'
php composer-setup.php --install-dir=/usr/local/opt/php@7.3/bin --filename=composer
php -r "unlink('composer-setup.php');"
```
Если у вас установлен старый `PHP 7.1`, выполните команду `brew unlink php@7.1` для удаления символьной ссылки, а затем выполните команду `brew link php@7.3` чтобы перейти к `PHP 7.3` и настроить `РНР` по умолчанию.
```bash
wenewzha:mixin_labs-php-bot wenewzhang$ brew unlink php@7.1
Unlinking /usr/local/Cellar/php/7.1.23... 24 symlinks removed
wenewzha:mixin_labs-php-bot wenewzhang$ brew link php@7.3
Warning: php@7.3 is keg-only and must be linked with --force

If you need to have this software first in your PATH instead consider running:
  echo 'export PATH="/usr/local/opt/php@7.3/bin:$PATH"' >> ~/.bash_profile
  echo 'export PATH="/usr/local/opt/php@7.3/sbin:$PATH"' >> ~/.bash_profile
```
После установки `php 7.3` и создания символьной ссылки, в зависимости от настроек операционной системы, если при выполнении команды `php -v` получен ответ `command not found`, добавьте строку `echo 'export PATH="/usr/local/opt/php@7.3/bin:$PATH"'` в `bash_profile`.
```bash
echo 'export PATH="/usr/local/opt/php@7.3/bin:$PATH"' >> ~/.bash_profile
echo 'export PATH="/usr/local/opt/php@7.3/sbin:$PATH"' >> ~/.bash_profile
source ~/.bash_profile
```

Для Ubuntu:
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
Последнюю версию менеджера зависимостей можно скачать [здесь](https://getcomposer.org/download/).
Убедитесь, что переменная `$PATH` содержит установочный каталог. Для проверки установки используйте следующую команду:
```bash
wenewzha:minecraft wenewzhang$ php -v
PHP 7.2.13 (cli) (built: Dec  7 2018 10:41:23) ( NTS )
Copyright (c) 1997-2018 The PHP Group
Zend Engine v3.2.0, Copyright (c) 1998-2018 Zend Technologies
    with Zend OPcache v7.2.13, Copyright (c) 1999-2018, by Zend Technologies
wenewzha:minecraft wenewzhang$ composer -V
Composer version 1.8.0 2018-12-03 10:31:16
```
### Создайте проект
Перейдите в папку `Documents`, затем создайте папку, например, `mixin_labs-php-bot`:

```bash
mkdir mixin_labs-php-bot
mixin_labs-php-bot
```
Выполните команду `composer init` в проектной папке, для создания `composer.json` следуйте инструкциям:
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
Для данного туториала требуются две библиотеки.
* [mixin-sdk-php](https://github.com/ExinOne/mixin-sdk-php) - это библиотека PHP SDK для Mixin Network.
* [Ratchet pawl](https://github.com/ratchetphp/Pawl) - это библиотека асинхронного клиента на вебсокете.

Добавьте обе библиотеки в `composer.json`, секцию `require`.
```bash
"require": {
    "exinone/mixin-sdk-php": "^1.1",
    "ratchet/pawl": "^0.3.3",
},
```
Сохраните `composer.json`, а затем выполните команду `composer install`, чтобы загрузить библиотеки.
```bash
composer install
```
После того, как в проектную папку загрузятся все библиотеки, в ней будет создана папка для вендора: 
```bash
root@iZj6cbmqen2lqp7l48nfgkZ:~/mixin_labs-php-bot# ls
composer.json  composer.lock  vendor
```
Если вы клонируете данный репозиторий с Github, просто выполните команду `composer install`, чтобы загрузить все библиотеки.

### Создайте своё первое приложение в панели инструментов для разработчиков Mixin Network
Вам нужно создать приложение в панели инструментов. Вам поможет этот [туториал](https://mixin-network.gitbook.io/mixin-network/mixin-messenger-app/create-bot-account).

### Сгенерируйте ключ вашего приложения в панели инструментов
После того, как вы создадите приложение в панели инструментов, вам нужно ещё [сгенерировать ключ](https://mixin-network.gitbook.io/mixin-network/mixin-messenger-app/create-bot-account#generate-secure-parameter-for-your-app)
и записать необходимое содержимое в `config.php`

![mixin_network-keys](https://github.com/wenewzhang/mixin_labs-php-bot/raw/master/mixin_network-keys.jpg)
Создайте в проектной папке файл `config.php`. Скопируйте в него следующее содержимое.
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
Замените значение **содержимым, сгенерированным в панели инструментов**.

### Первое приложение на PHP
Скопируйте в `app.php` следующий код (если этого файла в вашей папке нет, создайте его):
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
Запустите программу
```bash
php app.php
```
Консоль выведет следующее:
```bash
wenewzha:mixin_labs-php-bot wenewzhang$ php helloworld.php
a1ce2967-a534-417d-bf12-c86571e4eefa{"id":"4454b6c5-4a89-440c-bd22-7a79cf4954ca","action":"LIST_PENDING_MESSAGES"}stdClass Object
(
    [id] => 4454b6c5-4a89-440c-bd22-7a79cf4954ca
    [action] => LIST_PENDING_MESSAGES
)
```
Добавьте бота (например, этого бота — его ID 7000101639) в свой список контактов в [Mixin Messenger](https://mixin.one/messenger) и отправляйте сообщения.
![mixin_messenger](https://github.com/wenewzhang/mixin_labs-php-bot/raw/master/helloworld.jpeg)


### Краткое описание исходного кода
PHP-код создаёт клиента на вебсокете:
```php
$loop = \React\EventLoop\Factory::create();
$reactConnector = new \React\Socket\Connector($loop, [
    'timeout' => 15
]);
$connector = new \Ratchet\Client\Connector($loop,$reactConnector);
```

Код генерирует действительный токен и устанавливает соединение между веб-сокетом и сервером Mixin Messenger. Сообщения будут передаваться в клиент на веб-сокете.

[API по операциям](https://developers.mixin.one/api/beta-mixin-message/authentication/), [Руководство по операциям](https://mixin-network.gitbook.io/mixin-network/mixin-messenger-app/receive-asset-change-notification)

В библиотеке `mixin-sdk-php` есть реализация метода `getToken()`, вызовите его и сгенерируйте токен.
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
Подключитесь к серверу  Mixin messenger, используя сгенерированный токен.
```php
$connector('wss://blaze.mixin.one', ['protocol' => 'Mixin-Blaze-1'],[
                                    'Authorization' => 'Bearer '.$Token
                                      ])
```
Отправьте на сервер запрос `"LIST_PENDING_MESSAGES"`, чтобы получить сообщения, ожидающие доставки. 
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
Когда в клиент на вебсокете будет отправлено сообщение, будет вызвана функция `onMessage`.
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
Боту можно отправлять не только текст, изображения и т. п. Подробнее о сообщениях в Mixin Messenger [здесь](https://developers.mixin.one/api/beta-mixin-message/websocket-messages/).

Отправляйте на сервер сообщение `READ`, чтобы подтвердить прочтение. Если бот не отправит это сообщение, то сообщения будут ему дублироваться при каждом подключении к серверу. 
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
### Конец
Теперь бот работает, дальше дело за вами.

Полное описание кода [здесь](https://github.com/wenewzhang/mixin_labs-php-bot/blob/master/helloworld.php)
