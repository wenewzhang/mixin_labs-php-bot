<?php

    require __DIR__ . '/vendor/autoload.php';
    use ExinOne\MixinSDK\Traits\MixinSDKTrait;

    $loop = \React\EventLoop\Factory::create();
    // $reactConnector = new \React\Socket\Connector($loop, [
    //     'dns' => '8.8.8.8',
    //     'timeout' => 10
    // ]);
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
    $connector = new \Ratchet\Client\Connector($loop);

    // $connector('ws://127.0.0.1:9000', [], ['Origin' => 'http://localhost',
    $connector('wss://blaze.mixin.one', [], ['Origin' => 'wss://blaze.mixin.one',
                                          'Authorization' => 'Bearer '.$Token,
                                          'protocol' => 'Mixin-Blaze-1'])
    ->then(function(Ratchet\Client\WebSocket $conn) {
        $conn->on('message', function(\Ratchet\RFC6455\Messaging\MessageInterface $msg) use ($conn) {
            echo "Received: {$msg}\n";
            $conn->close();
        });

        $conn->on('close', function($code = null, $reason = null) {
            echo "Connection closed ({$code} - {$reason})\n";
        });

        $conn->send('Hello World!');
    }, function(\Exception $e) use ($loop) {
        echo "Could not connect: {$e->getMessage()}\n";
        $loop->stop();
    });

    $loop->run();
