# PHP Bitcoin tutorial based on Mixin Network II: Receive and send Bitcoin in Mixin Messenger
![](https://github.com/wenewzhang/mixin_labs-php-bot/raw/master/Bitcoin_php.jpg)

In [the previous chapter](https://github.com/wenewzhang/mixin_labs-php-bot/blob/master/README.md), your first bot just work. The bot echo message from user.

Create an app.php file with following code:
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
### Hello Bitcoin
Run **php app.php** in the project directory.

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
The bot connect to the mixin.one successfully when console output "LIST_PENDING_MESSAGES".

![pay-links](https://github.com/wenewzhang/mixin_labs-php-bot/raw/master/pay-links.jpg)

The bot has told you how to interact:
- **1** the bot send an APP_CARD link.
- **2** the bot send an APP_BUTTON_GROUP link.
- **? or help** for help!
Click the links in chatting page, input PIN code to pay coin to bot.
![click-pay-link-to-pay](https://github.com/wenewzhang/mixin_labs-php-bot/raw/master/click-link-to-pay.jpg)

Full [Mixin Messenger messages introduction](https://developers.mixin.one/api/beta-mixin-message/websocket-messages/).

User can pay 0.001 Bitcoin to bot, the 0.001 Bitcoin will be refunded in 1 seconds. In fact, user can pay any coins.
![pay-link](https://github.com/wenewzhang/mixin_network-nodejs-bot2/raw/master/Pay_and_refund_quickly.jpg)

Developer can send Bitcoin to their bots in chatting page. The bot will send back immediately after received Bitcoin.
![transfer  Bitcoin](https://github.com/wenewzhang/mixin_network-nodejs-bot2/raw/master/transfer-any-tokens.jpg)

## Source code summary
```php
$msg = new Frame(gzencode(json_encode($msgData)),true,Frame::OP_BINARY);
$conn->send($msg);
```
The bot send messages to user, Which use json serialize and then use gzencode compress it.

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

* The $dtPay->amount is negative if bot sends Bitcoin to user successfully.
* The $dtPay->amount is positive if bot receives Bitcoin.

Following code transfer the Bitcoin to user.
```php
function refundInstant($_assetID,$_amount,$_opponent_id) {
  $mixinSdk = new MixinSDK(require './config.php');
  // print_r();
  $BotInfo = $mixinSdk->Wallet()->transfer($_assetID,$_opponent_id,
                                           $mixinSdk->getConfig()['default']['pin'],$_amount);
  print_r($BotInfo);
}

```


Full code is [here](https://github.com/wenewzhang/mixin_labs-php-bot/blob/master/app.php)
