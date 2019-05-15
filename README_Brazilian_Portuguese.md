# Tutorial PHP de bitcoin baseado na Mixin Network. 
![](https://github.com/wenewzhang/mixin_labs-php-bot/raw/master/Bitcoin_php.jpg)
Um bot do mixin messenger será criado neste tutorial. O bot é desenvolvido por PHP e ecoa mensagem e Bitcoin do usuário.

[Recursos Mixin Network](https://github.com/awesome-mixin-network/index_of_Mixin_Network_resource)

## O que você aprenderá deste tutorial:
1. [Como criar bot no Mixin Messenger e responder uma mensagem para o usuário](https://github.com/wenewzhang/mixin_labs-php-bot/blob/master/README_Brazilian_Portuguese.md)|
2. [Como receber Bitcoin e enviar Bitcoin no Mixin Messenger](https://github.com/wenewzhang/mixin_labs-php-bot/blob/master/README2_Brazilian_Portuguese.md)|
3. [Como criar uma carteira Bitcoin baseada na API do Mixin Network](https://github.com/wenewzhang/mixin_labs-php-bot/blob/master/README3_Brazilian_Portuguese.md)|
4. [Como negociar bitcoin através da linguagem PHP](https://github.com/wenewzhang/mixin_labs-php-bot/blob/master/README4_Brazilian_Portuguese.md)
5. [Como negociar bitcoin através de PHP](https://github.com/wenewzhang/mixin_labs-php-bot/blob/master/README5_Brazilian_Portuguese.md)
## Criar um bot no Mixin Messenger e responder mensagem para o usuário
### Configuração do ambiente PHP:
Este tutorial é escrito em PHP 7. Então você precisa instalar [PHP](http://php.net/) e [Composer](https://getcomposer.org/).

No macOS
```bash
brew update
brew install php@7.3
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php -r "if (hash_file('sha384', 'composer-setup.php') === '48e3236262b34d30969dca3c37281b3b4bbe3221bda826ac6a9a62d6444cdb0dcd0615698a5cbe587c3f0fe57a54d8f5') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
//install composer to /usr/local/opt/php@7.3/bin and give a brief name 'composer'
php composer-setup.php --install-dir=/usr/local/opt/php@7.3/bin --filename=composer
php -r "unlink('composer-setup.php');"
```
Se você instalou um PHP passado antes do 7.1, execute **brew unlink php@7.1**  para remover os links de símbolos, e então execute, **brew link php@7.3** para ligar o php 7.3 ao **php** padrão
```bash
wenewzha:mixin_labs-php-bot wenewzhang$ brew unlink php@7.1
Unlinking /usr/local/Cellar/php/7.1.23... 24 symlinks removed
wenewzha:mixin_labs-php-bot wenewzhang$ brew link php@7.3
Warning: php@7.3 is keg-only and must be linked with --force

Se você precisa ter esse software primeiro no seu PATH considere ao invés disso executar:
  echo 'export PATH="/usr/local/opt/php@7.3/bin:$PATH"' >> ~/.bash_profile
  echo 'export PATH="/usr/local/opt/php@7.3/sbin:$PATH"' >> ~/.bash_profile
```
Depois do php 7.3 instalado e symlinks criados, de acordo com configurações do seu ambiente de SO, se **php -v** levar "command not found", emita **echo 'export PATH="/usr/local/opt/php@7.3/bin:$PATH"'** para o bash_profile.
```bash
echo 'export PATH="/usr/local/opt/php@7.3/bin:$PATH"' >> ~/.bash_profile
echo 'export PATH="/usr/local/opt/php@7.3/sbin:$PATH"' >> ~/.bash_profile
source ~/.bash_profile
```

No Ubuntu
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
O último compositor pode ser baixado [aqui](https://getcomposer.org/download/).
Certifique-se de que a variável $PATH contenha o diretório de instalação, o seguinte comando pode ser usado para checar a instalação
```bash
wenewzha:minecraft wenewzhang$ php -v
PHP 7.2.13 (cli) (built: Dec  7 2018 10:41:23) ( NTS )
Copyright (c) 1997-2018 The PHP Group
Zend Engine v3.2.0, Copyright (c) 1998-2018 Zend Technologies
    with Zend OPcache v7.2.13, Copyright (c) 1999-2018, by Zend Technologies
wenewzha:minecraft wenewzhang$ composer -V
Composer version 1.8.0 2018-12-03 10:31:16
```
### Crie o projeto
Vá até sua pasta de documentos e crie um diretório, por exemplo: **mixin_labs-php-bot**
```bash
mkdir mixin_labs-php-bot
mixin_labs-php-bot
```
Execute **composer init** no diretório do seu projeto, siga a instrução para criar o composer.json,
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
Esse tutorial exige duas bibliotecas.
* [mixin-sdk-php](https://github.com/ExinOne/mixin-sdk-php) é um PHP SDK para a Mixin Network.
* [Ratchet pawl](https://github.com/ratchetphp/Pawl) é um cliente de websocket assíncrono.

No arquivo composer.json, adicione as duas bibliotecas no código de bloco "require".
```bash
"require": {
    "exinone/mixin-sdk-php": "^1.1",
    "ratchet/pawl": "^0.3.3",
},
```
Salve o arquivo composer.json e então execute **composer install** para baixar as biblotecas exigidas.
```bash
composer install
```
Um diretório fornecedor é criado no diretório do projeto depois que todas bibliotecas são baixadas.
```bash
root@iZj6cbmqen2lqp7l48nfgkZ:~/mixin_labs-php-bot# ls
composer.json  composer.lock  vendor
```
Se você clonar esse repositório do repo Github, você apenas precisa executar **composer install** para baixar todas bibliotecas.

### Crie seu primeiro aplicativo no dashboard de desenvolvedor da Mixin Network 
Você precisa criar um app no dashboard. Esse [tutorial](https://mixin-network.gitbook.io/mixin-network/mixin-messenger-app/create-bot-account) pode te ajudar.

### Gere o parâmetro do seu app no dashboard
Depois do que o app é criado no dashboard, você ainda precisa [gerar parâmetro](https://mixin-network.gitbook.io/mixin-network/mixin-messenger-app/create-bot-account#generate-secure-parameter-for-your-app)
e escreva o conteúdo requerido, esse conteúdo será escrito no arquivo config.php.

![mixin_network-keys](https://i.ibb.co/NYF4Scr/mixin-network-keys.jpg)

Na pasta do projeto, crie o arquivo: config.php. Copie o seguinte conteúdo dentro.
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
Substitua o valor por **conteúdo gerado no dashboard**.

### Olá mundo em PHP
Copie o seguinte código em app.php, crie o arquivo app.php se ainda estiver faltando em sua pasta
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
Execute o código
```bash
php app.php
```
O seguinte conteúdo será mostrado no console.
```bash
wenewzha:mixin_labs-php-bot wenewzhang$ php helloworld.php
a1ce2967-a534-417d-bf12-c86571e4eefa{"id":"4454b6c5-4a89-440c-bd22-7a79cf4954ca","action":"LIST_PENDING_MESSAGES"}stdClass Object
(
    [id] => 4454b6c5-4a89-440c-bd22-7a79cf4954ca
    [action] => LIST_PENDING_MESSAGES
)
```
Adicione o bot (por exemplo, o id desse bot é 7000101639) como seu amigo no [Mixin Messenger](https://mixin.one/messenger) e envie suas mensagens.
![mixin_messenger](https://i.ibb.co/583s5XH/helloworld.jpg)


### Resumo do código fonte
O código PHP cria um cliente websocket.
```php
$loop = \React\EventLoop\Factory::create();
$reactConnector = new \React\Socket\Connector($loop, [
    'timeout' => 15
]);
$connector = new \Ratchet\Client\Connector($loop,$reactConnector);
```

O código gera um token válido e cria conexão entre o websocket e o servidor do Mixin Messenger. Mensagens serão empurradas para o cliente websocket.

[API da operação](https://developers.mixin.one/api/beta-mixin-message/authentication/), [Guia da operação](https://mixin-network.gitbook.io/mixin-network/mixin-messenger-app/receive-asset-change-notification)

O mixin-sdk-php implementa a função getToken, chame-a e gere um token.
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
Conecte-se ao servidor do Mixin Messenger com o token correto.
```php
$connector('wss://blaze.mixin.one', ['protocol' => 'Mixin-Blaze-1'],[
                                    'Authorization' => 'Bearer '.$Token
                                      ])
```
Envie "LIST_PENDING_MESSAGES" para o servidor para receber mensagens pendentes.
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
função **onMessage** será chamada quando a mensagem é empurrada para o cliente websocket.
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
Não apenas texto, imagens e outros tipos de mensagens serão empurradas para seu bot. Você pode achar mais [detalhes](https://developers.mixin.one/api/beta-mixin-message/websocket-messages/) sobre mensagens do Messenger.

Envie uma operação READ para o deixar o servidor saber que essa mensagem foi lida. O bot receberá a mensagem duplicada quando se conectar ao servidor novamente, se o bot não enviar resposta:
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
### Fim
Agora seu bot funcionou, você pode hackeá-lo.

O código completo está [aqui](https://github.com/wenewzhang/mixin_labs-php-bot/blob/master/helloworld.php)
