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
      protected $config;
      public function __construct()
      {
          $config = require(__DIR__.'/config.php');
          $this->config        = $config;
      }
    }
    $callTrait = new callTraitClass();
    $Token = $callTrait->getToken('GET', '/', '');
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
            if ($jsMsg->action === 'CREATE_MESSAGE') {
              echo "\nNeed reply server a receipt!\n";
              $IncomingMsg = ["message_id" => $jsMsg->data->message_id, "status" => "READ"];
      	      $RspMsg = ["id" => Uuid::uuid4()->toString(), "action" => "ACKNOWLEDGE_MESSAGE_RECEIPT",
                          "params" => $IncomingMsg];
              $msg = new Frame(gzencode(json_encode($RspMsg)),true,Frame::OP_BINARY);
              $conn->send($msg);
              if ($jsMsg->data->category === 'PLAIN_TEXT') {
                  echo "PLAIN_TEXT:".base64_decode($jsMsg->data->data);
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
