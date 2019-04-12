# Bagaimana cara berdagang bitcoin melalui bahasa PHP
![](https://github.com/wenewzhang/mixin_labs-php-bot/raw/master/Bitcoin_php.jpg)

## Solusi Pertama: bayar ke API ExinCore
[Exincore](https://github.com/exinone/exincore) provide a commercial trading API on Mixin Network.

Anda membayar USDT ke ExinCore, ExinCore mentransfer Bitcoin kepada Anda dengan cepat dengan biaya yang sangat rendah dan harga yang wajar. Setiap transaksi bersifat anonim untuk umum tetapi masih dapat diverifikasi di blockchain explorer. Hanya Anda dan ExinCore yang tahu detailnya.

ExinCore tidak tahu siapa Anda karena ExinCore hanya tahu uuid klien Anda.

### Pra-permintaan:
Anda seharusnya membuat bot berdasarkan pada Mixin Network. Buat satu dengan membaca [Tutorial PHP Bitcoin](https://github.com/wenewzhang/mixin_labs-php-bot).

#### Instal paket yang diperlukan
Seperti yang Anda ketahui, kami memperkenalkan Anda mixin-sdk-php di[bab 1](https://github.com/wenewzhang/mixin_labs-php-bot/blob/master/README.md), anggap sudah diinstal sebelumnya, mari kita instal **uuid, msgpack** disini.
```bash
  composer require ramsey/uuid
  composer require rybakit/msgpack
```
#### Setor (Deposit) USDT atau Bitcoin ke akun Mixin Network Anda dan lihat saldo
ExinCore dapat bertukar antara Bitcoin, USDT, EOS, Eth dll. Di sini menunjukkan kepada Anda cara bertukar antara USDT dan Bitcoin,
Periksa saldo & alamat dompet sebelum Anda melakukan pemesanan.

- Periksa alamat & saldo, ingat alamat dompet Bitcoin.
- Setor (Deposit) Bitcoin ke alamat dompet Bitcoin ini.
- Periksa saldo Bitcoin setelah 100 menit kemudian.

**Ngomong-ngomong, alamat Bitcoin & USDT sama.**

```php
if ($line == '2') {
  if (($handle = fopen("new_users.csv", "r")) !== FALSE) {
  while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
    $mixinSdk_eachAccountInstance = new MixinSDK(GenerateConfigByCSV($data));
    $asset_info = $mixinSdk_eachAccountInstance->Wallet()->readAsset(BTC_ASSET_ID);
    print_r("Bitcoin wallet address is :".$asset_info["public_key"]."\n");
    print_r("Bitcoin wallet balance is :".$asset_info["balance"]."\n");
  }
    fclose($handle);
  } else print("Create user first\n");
}
if ($line == '3') {
  if (($handle = fopen("new_users.csv", "r")) !== FALSE) {
  while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
    $mixinSdk_eachAccountInstance = new MixinSDK(GenerateConfigByCSV($data));
    $asset_info = $mixinSdk_eachAccountInstance->Wallet()->readAsset(USDT_ASSET_ID);
    print_r("USDT wallet address is :".$asset_info["public_key"]."\n");
    print_r("USDT wallet balance is :".$asset_info["balance"]."\n");
  }
    fclose($handle);
  } else print("Create user first\n");
}
```
#### Lihat harga pasar
Bagaimana cara mengecek harga koin? Anda perlu memahami apa koin dasar. Jika Anda ingin membeli Bitcoin dan menjual USDT, USDT adalah koin dasar. Jika Anda ingin membeli USDT dan menjual Bitcoin, Bitcoin adalah koin dasar.
```php
function getExchangeCoins($base_coin) :string {
  $client = new GuzzleHttp\Client();
  $res = $client->request('GET', 'https://exinone.com/exincore/markets?base_asset='.$base_coin, [
      ]);
  $result = "";
  if ($res->getStatusCode() == "200") {
    // echo $res->getStatusCode() . PHP_EOL;
    $resInfo = json_decode($res->getBody(), true);
    echo "Asset ID | Asset Symbol | Price | Amount | Exchanges" . PHP_EOL;
    $result = "Asset ID | Asset Symbol | Price | Amount | Exchanges" . PHP_EOL;
    foreach ($resInfo["data"] as $key => $coinInfo) {
      echo ($coinInfo["exchange_asset"] ." ".$coinInfo["exchange_asset_symbol"]. "/". $coinInfo["base_asset_symbol"] .
            " ". $coinInfo["price"] ." ". $coinInfo["minimum_amount"] ."-". $coinInfo["maximum_amount"] . " ");
      $result .= $coinInfo["exchange_asset_symbol"]. "/". $coinInfo["base_asset_symbol"] .
                  " ". $coinInfo["price"] ." ". $coinInfo["minimum_amount"] ."-". $coinInfo["maximum_amount"] . " ";
      foreach ($coinInfo["exchanges"] as $key => $exchange) {
        echo $exchange . " ";
        $result .= $exchange . " ";
      }
      echo PHP_EOL;
      $result .= PHP_EOL;
    }
  }
  return $result;
}
```

#### Buat memo untuk menyiapkan pesanan
Bab 2: [Echo Bitcoin](https://github.com/wenewzhang/mixin_labs-php-bot/blob/master/README2.md) memperkenalkan koin transfer. Tetapi Anda harus memberi tahu ExinCore koin mana yang ingin Anda beli. Cukup tulis aset target Anda ke dalam memo.
```php
$memo = base64_encode(MessagePack::pack([
                     'A' => Uuid::fromString($_targetAssetID)->getBytes(),
                     ]));
```
#### Bayar BTC ke gateway API dengan memo yang dihasilkan
Transfer Bitcoin(BTC_ASSET_ID) to ExinCore(EXIN_BOT), menempatkan Anda menargetkan aset uuid di memo, jika tidak, ExinCore akan mengembalikan uang Anda segera!
```php
const EXIN_BOT        = "61103d28-3ac2-44a2-ae34-bd956070dab1";
const BTC_ASSET_ID    = "c6d0c728-2624-429b-8e0d-d9d19b6592fa";
const EOS_ASSET_ID    = "6cfe566e-4aad-470b-8c9a-2fd35b49c68d";
const USDT_ASSET_ID   = "815b0b1a-2764-3736-8faa-42d694fa620a";
coinExchange(BTC_ASSET_ID,"0.0001",USDT_ASSET_ID);

//...........

function coinExchange($_assetID,$_amount,$_targetAssetID) {
  $mixinSdk = new MixinSDK(require './config.php');
  // print_r();
  $memo = base64_encode(MessagePack::pack([
                       'A' => Uuid::fromString($_targetAssetID)->getBytes(),
                       ]));
  $BotInfo = $mixinSdk->Wallet()->transfer($_assetID,EXIN_BOT,
                                           $mixinSdk->getConfig()['default']['pin'],$_amount,$memo);
  print_r($BotInfo);
}
```
ExinCore harus mentransfer koin target ke bot Anda, sementara itu, masukkan biaya, id pesanan, harga dll. informasi dalam memo itu, bukalah data seperti di bawah ini.
- **readUserSnapshots** Baca snapshot dari pengguna.
```php
$limit        = 20;
$offset       = '2019-03-10T01:58:25.362528Z';
$snapInfo = $mixinSdk_BotInstance->Wallet()->readUserSnapshots($limit, $offset);
// print_r($networkInfo2);
foreach ($snapInfo as  $record) {
  // echo $key . PHP_EOL;
  // print_r($record);
  if ($record['amount'] > 0 and $record['memo'] != '') {
    echo "------------MEMO:-coin--exchange--------------" . PHP_EOL;
    echo "memo: " . $record['memo'] . PHP_EOL;
    // print_r($dtPay->memo);
    echo "You Get Coins: ". $record['asset_id']. " " . $record['amount'] . PHP_EOL;
    $memoUnpack = MessagePack::unpack(base64_decode($record['memo']));
    $feeAssetID = Uuid::fromBytes($memoUnpack['FA'])->toString();
    $OrderID    = Uuid::fromBytes($memoUnpack['O'])->toString();
    if ($memoUnpack['C'] == 1000) {
      echo "Successful Exchange:". PHP_EOL;
      echo "Fee asset ID: " . $feeAssetID . " fee is :" . $memoUnpack['F'] . PHP_EOL;
      echo "Order ID: " . $OrderID . " Price is :" . $memoUnpack['P'] . PHP_EOL;
    } else print_r($memoUnpack);
    echo "--------------memo-record end---------------" . PHP_EOL;
  }
}
```

Jika pertukaran uang Anda berhasil, hasilkan konsol seperti di bawah ini:
```bash
------------MEMO:-coin--exchange--------------
memo: hqFDzQPooVCnMzg3Mi45N6FGqTAuMDAwNzc0NqJGQcQQgVsLGidkNzaPqkLWlPpiCqFUoUahT8QQIbfeL6p5RVOcEP0mLb+t+g==
You Get Coins: 815b0b1a-2764-3736-8faa-42d694fa620a 0.3857508
Successful Exchange:
Fee asset ID: 815b0b1a-2764-3736-8faa-42d694fa620a fee is :0.0007746
Order ID: 21b7de2f-aa79-4553-9c10-fd262dbfadfa Price is :3872.97
--------------memo-record end---------------
```

#### Lihat saldo Bitcoin
Periksa saldo dompet.
```php
$mixinSdk = new MixinSDK(require './config.php');
$asset_info = $mixinSdk->Wallet()->readAsset(USDT_ASSET_ID);
print_r("USDT wallet balance is :".$asset_info["balance"]."\n");
```
## Penggunaan kode sumber
Jalankan **php call_apis.php** untuk menjalankannya.

- 1: Buat pengguna dan perbarui PIN
- 2: Lihat saldo & alamat Bitcoin
- 3: Lihat saldo & alamat USDT
- 4: Lihat saldo EOS
- 5: Lihat alamat EOS
- 6: Transfer Bitcoin dari bot ke pengguna baru
- 7: Transfer Bitcoin dari pengguna baru ke Master
- 8: Tarik (Withdraw) Bitcoin bot
- 9: Tarik (Withdraw) EOS bot
- qu: Lihat harga pasar (USDT)
- qb: Lihat harga pasar (BTC)
- b: Saldo bot (USDT & BTC)
- s: Lihat Snapshots
- tb: Transfer 0,0001 BTC, beli USDT
- tu: Transfer $ 1 USDT, beli BTC
- q: Keluar

[Lengkap Kode Sumber](https://github.com/wenewzhang/mixin_labs-php-bot/blob/master/call_apis.php)

## Solusi Kedua: Daftarkan pesanan Anda di bursa Ocean.One
