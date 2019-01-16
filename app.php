<?php

    require __DIR__ . '/vendor/autoload.php';
    use ExinOne\MixinSDK\Traits\MixinSDKTrait;
    use Ramsey\Uuid\Uuid;
    use Ratchet\RFC6455\Messaging\Frame;

    $loop = \React\EventLoop\Factory::create();
    $reactConnector = new \React\Socket\Connector($loop, [
        'dns' => '8.8.8.8',
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
              $IncomingMsg = ["message_id" => $jsMsg->data->message_id, "status" => "READ"];
      	      $RspMsg = ["id" => Uuid::uuid4()->toString(), "action" => "ACKNOWLEDGE_MESSAGE_RECEIPT",
                          "params" => $IncomingMsg];
              $msg = new Frame(gzencode(json_encode($RspMsg)),true,Frame::OP_BINARY);
              $conn->send($msg);

              if ($jsMsg->data->category === 'PLAIN_TEXT') {
                  echo "PLAIN_TEXT:".base64_decode($jsMsg->data->data);
                  $isCmd = strtolower(base64_decode($jsMsg->data->data));
                  if ($isCmd = 'pay') {
                     // print($callTrait->config['client_id']);
                     $payLink = "https://mixin.one/pay?recipient=".
                                  "a1ce2967-a534-417d-bf12-c86571e4eefa"."&asset=".
                                  "6cfe566e-4aad-470b-8c9a-2fd35b49c68d".
                                  "&amount=0.0001"."&trace=".Uuid::uuid4()->toString().
                                  "&memo=";
                      $msgData = [[
                          'label'    =>  "Pay 0.001 EOS",
                          'color'       =>  "#ABABAB",
                          'action'      =>  $payLink,
                        ]
                      ];
                      $msgParams = [
                        'conversation_id' => $jsMsg->data->conversation_id,// $callTrait->config[client_id],
                        // 'recipient_id'    => $jsMsg->data->user_id,
                        'category'        => 'APP_BUTTON_GROUP',//'PLAIN_TEXT',
                        'status'          => 'SENT',
                        'message_id'      => Uuid::uuid4()->toString(),
                        'data'            => base64_encode(json_encode($msgData)),//base64_encode("hello!"),
                      ];
                      $msgPayButton = [
                        'id'     =>  Uuid::uuid4()->toString(),
                        'action' =>  'CREATE_MESSAGE',
                        'params' =>   $msgParams,
                      ];
                      print_r($msgPayButton);
                      print_r(json_encode($msgPayButton));
                      $msg = new Frame(gzencode(json_encode($msgPayButton)),true,Frame::OP_BINARY);
                      $conn->send($msg);
                  }
              }
            }
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
