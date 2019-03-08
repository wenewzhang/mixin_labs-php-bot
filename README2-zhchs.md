
# 基于Mixin Network的PHP比特币开发教程: 机器人接受比特币并立即退还用户
![](https://github.com/wenewzhang/mixin_labs-php-bot/raw/master/Bitcoin_php.jpg)

在 [上一篇教程中](https://github.com/wenewzhang/mixin_labs-php-bot/blob/master/README-zhchs.md), 我们创建了自动回复消息的机器人,当用户发送消息"Hello,World!"时，机器人会自动回复同一条消息!


按本篇教程后学习后完成后，你的机器人将会接受用户发送过来的加密货币，然后立即转回用户。下面是全部源代码，创建一个app.php试一下吧！

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
### 你好，我的币!
在工程目录下，执行 **php app.php**

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
如果控制台出现 "LIST_PENDING_MESSAGES"字样, 连接到mixin.one成功了，正在侦听用户发送消息给它!

![pay-links](https://github.com/wenewzhang/mixin_labs-php-bot/raw/master/pay-links.jpg)

按帮助来操作,发送消息得到相应的支付提示
- **1** 机器人回复 APP_CARD 支付链接.
- **2** 机器人回复 APP_BUTTON_GROUP 支付链接.
- **? or help** : 显示帮助
点击上面的链接，将会弹出一个窗口，输入你的密码，将支付币给机器人!
![click-pay-link-to-pay](https://github.com/wenewzhang/mixin_labs-php-bot/raw/master/click-link-to-pay.jpg)

[Mixin Messenger支持的消息类型](https://developers.mixin.one/api/beta-mixin-message/websocket-messages/)

如下图所示，用户点击支付链接，输入密码，支付0.01 EOS给机器人，机器人马上返还给用户！
![pay-link](https://github.com/myrual/mixin_network-nodejs-bot2/raw/master/Pay_and_refund_quickly.jpg)

亲爱的开发者，你也可以从消息控制面板里，点击转帐，直接将币转给机器人！它还是一样的立即返还！
![transfer and tokens](https://github.com/wenewzhang/mixin_network-nodejs-bot2/raw/master/transfer-any-tokens.jpg)

## 源代码解释
```php
$msg = new Frame(gzencode(json_encode($msgData)),true,Frame::OP_BINARY);
$conn->send($msg);
```
机器人发送给用户的消息，先用json序列化，再用gzencode压缩。

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
如果机器人收到币，
```php
$dtPay->amount
```
大于零；如果机器人支付币给用户，接收到的消息是一样的，唯一不同的是
```php
$dtPay->amount
```
是一个负数.

```php
function refundInstant($_assetID,$_amount,$_opponent_id) {
  $mixinSdk = new MixinSDK(require './config.php');
  // print_r();
  $BotInfo = $mixinSdk->Wallet()->transfer($_assetID,$_opponent_id,
                                           $mixinSdk->getConfig()['default']['pin'],$_amount);
  print_r($BotInfo);
}

```
最后一步，调用MixinSDK将币还给用户！

完整的代码在这儿 [here](https://github.com/wenewzhang/mixin_labs-php-bot/blob/master/app.php)
