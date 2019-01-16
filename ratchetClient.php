<?php

    require __DIR__ . '/vendor/autoload.php';
    use ExinOne\MixinSDK\Traits\MixinSDKTrait;
    use Ramsey\Uuid\Uuid;

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
            $jsMsg = gzdecode($msg);
            echo "Received: {$jsMsg}\n";
            // $conn->close();
            // echo gzdecode($msg);
        });

        $conn->on('close', function($code = null, $reason = null) {
            echo "Connection closed ({$code} - {$reason})\n";
        });
        $message = [
            'id'     => Uuid::uuid4()->toString(),
            'action' => 'LIST_PENDING_MESSAGES',
        ];
        print_r($message);
        $jsMsg = json_encode($message);
        echo "Send: {$jsMsg}\n";
        $conn->send(gzencode($jsMsg));
    }, function(\Exception $e) use ($loop) {
        echo "Could not connect: {$e->getMessage()}\n";
        $loop->stop();
    });

    $loop->run();
