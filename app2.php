<?php
namespace App2;
require 'vendor/autoload.php';
class App2
{
    protected $mixinSDK;

    use ExinOne\MixinSDK\MixinSDK;

    // $mixinSdk = new MixinSDK(require './config.php');
    public static function main(bool $exit = true): int
    {
        $command = new static;
        $this->mixinSDK = new MixinSDK(require './config.php');
        // test_it_can_send_message();
        $ids = [
            '0b4f49dc-8fb4-4539-9a89-fb3afc613747',
        ];
        $a = $this->mixinSDK->message()->sendBatchMessage($ids,'Hello,Websocket!?');
    }
    public function test_it_can_send_message()
    {
        $ids = [
            '0b4f49dc-8fb4-4539-9a89-fb3afc613747',
        ];
        $a = $this->mixinSDK->message()->sendBatchMessage($ids,'Hello,Websocket!?');
    }
}
App2::main();
