# Tutorial PHP Bitcoin berdasarkan Mixin Network
![](https://github.com/wenewzhang/mixin_labs-php-bot/raw/master/Bitcoin_php.jpg)
Bot messenger Mixin akan dibuat dalam tutorial ini. Bot ini didukung oleh PHP dan pesan gema dan Bitcoin dari pengguna.

[Sumbebr Daya Jaringan Mixin](https://github.com/awesome-mixin-network/index_of_Mixin_Network_resource)

## Apa yang akan Anda pelajari dari tutorial ini
1. [Cara membuat bot di Mixin messenger dan membalas pesan ke pengguna](https://github.com/wenewzhang/mixin_labs-php-bot#create-bot-and-receive-message-from-user)
2. [Cara menerima Bitcoin dan mengirim Bitcoin di Mixin Messenger](https://github.com/wenewzhang/mixin_labs-php-bot/blob/master/README2.md)
3. [Cara membuat dompet Bitcoin berdasarkan Mixin Network API](https://github.com/wenewzhang/mixin_labs-php-bot/blob/master/README3.md)
# Buat bot di Mixin messenger dan balas pesan ke pengguna
# Penyiapan set untuk PHP:
Tutorial ini ditulis dalam PHP 7. Jadi Anda perlu menginstal PHP dan Komposer .

Di macOS
```bash
brew update
brew install php@7.3
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php -r "if (hash_file('sha384', 'composer-setup.php') === '48e3236262b34d30969dca3c37281b3b4bbe3221bda826ac6a9a62d6444cdb0dcd0615698a5cbe587c3f0fe57a54d8f5') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
//install composer to /usr/local/opt/php@7.3/bin and give a brief name 'composer'
php composer-setup.php --install-dir=/usr/local/opt/php@7.3/bin --filename=composer
php -r "unlink('composer-setup.php');"
```
Jika Anda menginstal php 7.1 lama sebelumnya, jalankan brew unlink php@7.1 untuk menghapus tautan simbol, dan kemudian jalankan brew link php@7.3 untuk menghubungkan php 7.3 ke php default
```bash
wenewzha:mixin_labs-php-bot wenewzhang$ brew unlink php@7.1
Unlinking /usr/local/Cellar/php/7.1.23... 24 symlinks removed
wenewzha:mixin_labs-php-bot wenewzhang$ brew link php@7.3
Warning: php@7.3 is keg-only and must be linked with --force

If you need to have this software first in your PATH instead consider running:
  echo 'export PATH="/usr/local/opt/php@7.3/bin:$PATH"' >> ~/.bash_profile
  echo 'export PATH="/usr/local/opt/php@7.3/sbin:$PATH"' >> ~/.bash_profile
```
Setelah php 7.3 diinstal dan symlink dibuat, sesuai pengaturan lingkungan OS Anda, jika **php -v** prompt "command not found", keluarkan **echo 'export PATH="/usr/local/opt/php@7.3/bin:$PATH** ke bash_profile.
```bash
echo 'export PATH="/usr/local/opt/php@7.3/bin:$PATH"' >> ~/.bash_profile
echo 'export PATH="/usr/local/opt/php@7.3/sbin:$PATH"' >> ~/.bash_profile
source ~/.bash_profile
```

Di Ubuntu
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
Komposer terbaru dapan diunduh [disini](https://getcomposer.org/download/)
Dan pastikan variabel $PATH untung menginstal, perintah berikut dapat digunakan untuk memeriksa instalasi
```bash
wenewzha:minecraft wenewzhang$ php -v
PHP 7.2.13 (cli) (built: Dec  7 2018 10:41:23) ( NTS )
Copyright (c) 1997-2018 The PHP Group
Zend Engine v3.2.0, Copyright (c) 1998-2018 Zend Technologies
    with Zend OPcache v7.2.13, Copyright (c) 1999-2018, by Zend Technologies
wenewzha:minecraft wenewzhang$ composer -V
Composer version 1.8.0 2018-12-03 10:31:16
```
### Buat Proyek
Buka folder dokumen Anda lalu buat direktori, contohnya: **mixin_labs-php-bot**
```bash
mkdir mixin_labs-php-bot
mixin_labs-php-bot
```
Jalankan **composer init** didalam proyek directory anda, ikuti instruksi untuk membuat composer.json,
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
Tutorial ini membutuhkan 2 komponen
* [mixin-sdk-php](https://github.com/ExinOne/mixin-sdk-php) is a PHP SDK for Mixin Network.
* [Ratchet pawl](https://github.com/ratchetphp/Pawl) is a asynchronous websocket client.

Dalam file composer.json, tambahkan dua perpustakaan di blok kode "wajib".
```bash
"require": {
    "exinone/mixin-sdk-php": "^1.1",
    "ratchet/pawl": "^0.3.3",
},
```
Simpan file composer.json dan kemudian jalankan komposer untuk mengunduh pustaka yang diperlukan.
```bash
composer install
```
Direktory vendor dibuat di direktory proyek setelah semua perpustakaan diunduh.
```bash
root@iZj6cbmqen2lqp7l48nfgkZ:~/mixin_labs-php-bot# ls
composer.json  composer.lock  vendor
```
Jika Anda mengkloning repositori ini dari repo Github, Anda hanya perlu menjalankan **composer install** Untuk mengunduh seluruh fuction/library.

### Buat aplikasi pertama Anda di dasbor pengembang Mixin Network
Anda perlu membuat aplikasi di dasboard. [tutorial] ini(https://mixin-network.gitbook.io/mixin-network/mixin-messenger-app/create-bot-account) dapat membantu anda.

### Hasilkan parameter aplikasi Anda di dasbord
Setelah aplikasi terbuat didasboard, anda masih perlu [membuat parameter](https://mixin-network.gitbook.io/mixin-network/mixin-messenger-app/create-bot-account#generate-secure-parameter-for-your-app)
menuliskan konten yang diperlukan, konten ini akan ditulis ke file config.php.

![mixin_network-keys](https://github.com/wenewzhang/mixin_labs-php-bot/raw/master/mixin_network-keys.jpg)
Dalam folder proyek, buat file: config.php. Salin konten berikut ke dalamnya.
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
Ganti nilai dengan **content yang telah dibuat didashboard**.

### Hello world dalam PHP
Salin kode berikut ke app.php, buat file app.php jika tidak ada di folder Anda
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
Jalankan Kodenya
```bash
php app.php
```
Konten berikut akan ditampilkan di konsol.
```bash
wenewzha:mixin_labs-php-bot wenewzhang$ php helloworld.php
a1ce2967-a534-417d-bf12-c86571e4eefa{"id":"4454b6c5-4a89-440c-bd22-7a79cf4954ca","action":"LIST_PENDING_MESSAGES"}stdClass Object
(
    [id] => 4454b6c5-4a89-440c-bd22-7a79cf4954ca
    [action] => LIST_PENDING_MESSAGES
)
```
Tambahkan Bot(misalnya, id bot ini adalah 7000101639) sebagai teman Anda di [Mixin Messenger](https://mixin.one/messenger) dan kirim pesan Anda.
![mixin_messenger](https://github.com/wenewzhang/mixin_labs-php-bot/raw/master/helloworld.jpeg)


### Ringkasan summary code
Kode PHP menciptakan klien websocket.
```php
$loop = \React\EventLoop\Factory::create();
$reactConnector = new \React\Socket\Connector($loop, [
    'timeout' => 15
]);
$connector = new \Ratchet\Client\Connector($loop,$reactConnector);
```

Kode menghasilkan token yang valid dan membuat koneksi antara websocket dan server Mixin Messenger. Pesan akan didorong ke klien websocket.

[API mengoprasikan](https://developers.mixin.one/api/beta-mixin-message/authentication/), [Panduan pengoprasian](https://mixin-network.gitbook.io/mixin-network/mixin-messenger-app/receive-asset-change-notification)

Mixin-sdk-php mengimplementasikan fungsi getToken, memanggilnya dan menghasilkan token.
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
Terhubung ke server messenger Mixin dengan token yang benar.
```php
$connector('wss://blaze.mixin.one', ['protocol' => 'Mixin-Blaze-1'],[
                                    'Authorization' => 'Bearer '.$Token
                                      ])
```
Kirim "LIST_PENDING_MESSAGES" ke server untuk menerima pesan yang tertunda.
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
Fungsi **onMessage** akan dipanggil ketika pesan didorong ke klien websocket.
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
Tidak hanya teks, gambar, dan pesan jenis lainnya akan didorong ke bot Anda. Anda dapat menemukan [lebih lengkap](https://developers.mixin.one/api/beta-mixin-message/websocket-messages/)tentang pesan Messenger.

Kirim pesan operasi BACA ke server agar tahu bahwa pesan ini telah dibaca. Bot akan menerima pesan yang digandakan ketika bot terhubung ke server lagi jika bot tidak mengirim respons.

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
### Penutup
Sekarang Bot anda akan berfungsi,dan anda dapat meretasnya
Kode lengkap ada [disini](https://github.com/wenewzhang/mixin_labs-php-bot/blob/master/helloworld.php)
