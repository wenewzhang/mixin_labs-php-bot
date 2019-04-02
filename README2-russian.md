# Туториал по PHP для работы с биткойном через Mixin Network II: Получайте и отправляйте Bitcoin в Mixin Messenger
![](https://github.com/wenewzhang/mixin_labs-php-bot/raw/master/Bitcoin_php.jpg)

В [предыдущей главе](https://github.com/wenewzhang/mixin_labs-php-bot/blob/master/README.md) ваш первый бот начал работать. Это echo-бот, он возвращал сообщения. 

Создайте файл app.php с таким кодом:
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
print_r($callTrait->config['client_id']);
// $Header = 'Authorization'.'Bearer '.$Token;
// print($Header);
$connector = new \Ratchet\Client\Connector($loop,$reactConnector);
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
              echo "PLAIN_TEXT:".base64_decode($jsMsg->data->data);
              $isCmd = strtolower(base64_decode($jsMsg->data->data));
              if ($isCmd ==='?' or $isCmd ==='help') {
                  $msgData = sendUsage($jsMsg->data->conversation_id);
                  $msg = new Frame(gzencode(json_encode($msgData)),true,Frame::OP_BINARY);
                  $conn->send($msg);
              } elseif ($isCmd === '1') {
                 // print($callTrait->config['client_id']);
                  $msgData = sendAppButtons($jsMsg);
                  $msg = new Frame(gzencode(json_encode($msgData)),true,Frame::OP_BINARY);
                  $conn->send($msg);
              }//end of pay1

              elseif ($isCmd === '2') {
                 // print($callTrait->config['client_id']);
                  $msgData = sendAppCard($jsMsg);
                  $msg = new Frame(gzencode(json_encode($msgData)),true,Frame::OP_BINARY);
                  $conn->send($msg);
              }//end of pay2
              elseif ($isCmd === '3') {
                  transfer();
              } else {
                  $msgData = sendPlainText($jsMsg->data->conversation_id,
                                            base64_decode($jsMsg->data->data));
                  $msg = new Frame(gzencode(json_encode($msgData)),true,Frame::OP_BINARY);
                  $conn->send($msg);
              }
          } //end of PLAIN_TEXT
          if ($jsMsg->data->category === 'SYSTEM_ACCOUNT_SNAPSHOT') {
            // refundInstant
              echo "user id:".$jsMsg->data->user_id;
              $dtPay = json_decode(base64_decode($jsMsg->data->data));
              print_r($dtPay);
              if ($dtPay->amount > 0) {
                echo "paid!".$dtPay->asset_id;
                refundInstant($dtPay->asset_id,$dtPay->amount,$jsMsg->data->user_id);
              }
          } //end of SYSTEM_ACCOUNT_SNAPSHOT
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


function sendUsage($conversation_id):Array {
  $msgHelp = <<<EOF
   Usage:
   ? or help : for help!
   1         : pay by APP_CARD
   2         : pay by APP_BUTTON_GROUP
EOF;
  return sendPlainText($conversation_id,$msgHelp);
}

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
function sendAppButtons($jsMsg):Array {
  $payLinkEOS = "https://mixin.one/pay?recipient=".
               "a1ce2967-a534-417d-bf12-c86571e4eefa"."&asset=".
               "6cfe566e-4aad-470b-8c9a-2fd35b49c68d".
               "&amount=0.0001"."&trace=".Uuid::uuid4()->toString().
               "&memo=";
  $payLinkBTC = "https://mixin.one/pay?recipient=".
                "a1ce2967-a534-417d-bf12-c86571e4eefa"."&asset=".
                "c6d0c728-2624-429b-8e0d-d9d19b6592fa".
                "&amount=0.0001"."&trace=".Uuid::uuid4()->toString().
                "&memo=";
   $msgData = [[
       'label'    =>  "Pay 0.001 EOS",
       'color'       =>  "#FFABAB",
       'action'      =>  $payLinkEOS,
     ],[
         'label'    =>  "Pay 0.0001 BTC",
         'color'       =>  "#00EEFF",
         'action'      =>  $payLinkBTC,
       ],
   ];
   $msgParams = [
     'conversation_id' => $jsMsg->data->conversation_id,// $callTrait->config[client_id],
     // 'recipient_id'    => $jsMsg->data->user_id,
     'category'        => 'APP_BUTTON_GROUP',//'PLAIN_TEXT',
     'status'          => 'SENT',
     'message_id'      => Uuid::uuid4()->toString(),
     'data'            => base64_encode(json_encode($msgData)),//base64_encode("hello!"),
   ];
   $msgPayButtons = [
     'id'     =>  Uuid::uuid4()->toString(),
     'action' =>  'CREATE_MESSAGE',
     'params' =>   $msgParams,
   ];
   return $msgPayButtons;
}

function sendAppCard($jsMsg):Array
{
  $payLink = "https://mixin.one/pay?recipient=".
               "a1ce2967-a534-417d-bf12-c86571e4eefa"."&asset=".
               "6cfe566e-4aad-470b-8c9a-2fd35b49c68d".
               "&amount=0.0001"."&trace=".Uuid::uuid4()->toString().
               "&memo=";
   $msgData = [
       'icon_url'    =>  "https://mixin.one/assets/98b586edb270556d1972112bd7985e9e.png",
       'title'       =>  "Pay 0.001 EOS",
       'description' =>  "pay",
       'action'      =>  $payLink,
   ];
   $msgParams = [
     'conversation_id' => $jsMsg->data->conversation_id,// $callTrait->config[client_id],
     // 'recipient_id'    => $jsMsg->data->user_id,
     'category'        => 'APP_CARD',//'PLAIN_TEXT',
     'status'          => 'SENT',
     'message_id'      => Uuid::uuid4()->toString(),
     'data'            => base64_encode(json_encode($msgData)),//base64_encode("hello!"),
   ];
   $msgPayButton = [
     'id'     =>  Uuid::uuid4()->toString(),
     'action' =>  'CREATE_MESSAGE',
     'params' =>   $msgParams,
   ];
   return $msgPayButton;
}

function transfer() {
  $mixinSdk = new MixinSDK(require './config.php');
  print_r($mixinSdk->getConfig());
}

function generateReceipt($msgID):Array {
  $IncomingMsg = ["message_id" => $msgID, "status" => "READ"];
  $RspMsg = ["id" => Uuid::uuid4()->toString(), "action" => "ACKNOWLEDGE_MESSAGE_RECEIPT",
              "params" => $IncomingMsg];
  return $RspMsg;
}
function refundInstant($_assetID,$_amount,$_opponent_id) {
  $mixinSdk = new MixinSDK(require './config.php');
  // print_r();
  $BotInfo = $mixinSdk->Wallet()->transfer($_assetID,$_opponent_id,
                                           $mixinSdk->getConfig()['default']['pin'],$_amount);
  print_r($BotInfo);
}
```
### Начало работы с биткойном
Запустите **php app.php** в проектной папке.

```bash
php app.php
```

```bash
wenewzha:mixin_labs-php-bot wenewzhang$ php app.php
a1ce2967-a534-417d-bf12-c86571e4eefa{"id":"12c7a470-d6a4-403d-94e8-e6f8ae833971","action":"LIST_PENDING_MESSAGES"}stdClass Object
(
    [id] => 12c7a470-d6a4-403d-94e8-e6f8ae833971
    [action] => LIST_PENDING_MESSAGES
)
```
Бот успешно подключен к mixin.one, если консоль выводит "LIST_PENDING_MESSAGES".

![pay-links](https://github.com/wenewzhang/mixin_labs-php-bot/raw/master/pay-links.jpg)

Бот рассказывает, как с ним взаимодействовать:
- Наберите **1** для отправки ссылки APP_CARD;
- Наберите **2** для отправки ссылки APP_BUTTON_GROUP;
- Наберите **? или help** для получения помощи.
Выберите нужную кнопку в окне сообщений, введите ПИН-код, и бот получит оплату.
![click-pay-link-to-pay](https://github.com/wenewzhang/mixin_labs-php-bot/raw/master/click-link-to-pay.jpg)

Всё [о сообщениях в Mixin Messenger](https://developers.mixin.one/api/beta-mixin-message/websocket-messages/).

Вы можете заплатить боту 0.001 биткойн, и 0.001 биткойн будет возвращен в течение 1 секунды. Оплата может быть в любой криптовалюте.
![pay-link](https://github.com/wenewzhang/mixin_network-nodejs-bot2/raw/master/Pay_and_refund_quickly.jpg)

Разработчики могут отправлять биткойны своим ботам на странице чата. Бот получает биткойн и сразу же возвращает.
![transfer  Bitcoin](https://github.com/wenewzhang/mixin_network-nodejs-bot2/raw/master/transfer-any-tokens.jpg)

## Краткое описание исходного кода
```php
$msg = new Frame(gzencode(json_encode($msgData)),true,Frame::OP_BINARY);
$conn->send($msg);
```
При отправке сообщений пользователю используется json для сериализации и gzencode для сжатия.

```php
if ($jsMsg->data->category === 'SYSTEM_ACCOUNT_SNAPSHOT') {
  // refundInstant
    echo "user id:".$jsMsg->data->user_id;
    $dtPay = json_decode(base64_decode($jsMsg->data->data));
    print_r($dtPay);
    if ($dtPay->amount > 0) {
      echo "paid!".$dtPay->asset_id;
      refundInstant($dtPay->asset_id,$dtPay->amount,$jsMsg->data->user_id);
    }
} //end of SYSTEM_ACCOUNT_SNAPSHOT
```

– Если бот отправил биткойн пользователю, то сумма $dtPay->amount отрицательна.
– Если бот получает биткойн от пользователя, то сумма $dtPay->amount положительна.

Биткойн переводится пользователю с помощью следующего кода:
```php
function refundInstant($_assetID,$_amount,$_opponent_id) {
  $mixinSdk = new MixinSDK(require './config.php');
  // print_r();
  $BotInfo = $mixinSdk->Wallet()->transfer($_assetID,$_opponent_id,
                                           $mixinSdk->getConfig()['default']['pin'],$_amount);
  print_r($BotInfo);
}

```


Полное описание кода [здесь](https://github.com/wenewzhang/mixin_labs-php-bot/blob/master/app.php)
