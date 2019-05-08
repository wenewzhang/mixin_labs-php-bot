# Bagaimana cara berdagang bitcoin melalui PHP
![](https://github.com/wenewzhang/mixin_labs-php-bot/raw/master/Bitcoin_php.jpg)

Exincore diperkenalkan dalam [bab terakhir](https://github.com/wenewzhang/mixin_labs-php-bot/blob/master/README4.md), Anda dapat bertukar banyak aset kripto  dengan harga pasar dan menerima aset Anda dalam 1 detik. Jika Anda ingin memperdagangkan aset dengan harga terbatas, atau memperdagangkan aset tidak didukung oleh ExinCore sekarang, Ocean.One adalah jawabannya.

## Solusi Kedua: Daftarkan pesanan Anda di bursa Ocean.One
[Ocean.one](https://github.com/mixinNetwork/ocean.one) adalah pertukaran terdesentralisasi yang dibangun di atas Mixin Network, hampir pertama kali pertukaran terdesentralisasi mendapatkan pengalaman pengguna yang sama dengan yang terpusat.

Anda dapat membuat daftar aset apa pun di Ocean.One. Bayar aset yang ingin Anda jual ke akun Ocean.One, tulis permintaan Anda di memo pembayaran, Ocean.One akan mencantumkan pesanan Anda ke pasar. Ini mengirim aset ke dompet Anda setelah pesanan Anda cocok.

* Tidak perlu mendaftar
* Tidak diperlukan deposit
* Tidak ada proses daftar.

### Pra-permintaan:
Anda seharusnya membuat bot berdasarkan pada Mixin Network. Buat satu dengan membaca [Tutorial PHP Bitcoin](https://github.com/wenewzhang/mixin_labs-php-bot/blob/master/README.md).

#### Instal paket yang diperlukan
[Bab 4](https://github.com/wenewzhang/mixin_labs-php-bot/blob/master/README4.md) memperkenalkan [**mixin-sdk-php**](https://packagist.org/packages/exinone/mixin-sdk-php) untuk Anda, anggap sudah diinstal sebelumnya.

#### Deposit USDT atau Bitcoin ke akun Mixin Network Anda dan baca saldo
Ocean.one dapat mencocokkan pesanan apa pun. Di sini kami bertukar antara USDT dan Bitcoin, Periksa saldo & alamat dompet sebelum Anda melakukan pemesanan.

- Periksa alamat & saldo, temukan alamat dompet Bitcoin itu.
- Deposit Bitcoin ke alamat dompet Bitcoin ini.
- Periksa saldo Bitcoin setelah 100 menit kemudian.

**Alamat Omni USDT sama dengan alamat Bitcoin**

```php
  const BTC_ASSET_ID     = "c6d0c728-2624-429b-8e0d-d9d19b6592fa";
  const EOS_ASSET_ID     = "6cfe566e-4aad-470b-8c9a-2fd35b49c68d";
  const USDT_ASSET_ID    = "815b0b1a-2764-3736-8faa-42d694fa620a";

  $mixinSdk_eachAccountInstance = new MixinSDK(GenerateConfigByCSV($data));
  $asset_info = $mixinSdk_eachAccountInstance->Wallet()->readAsset(BTC_ASSET_ID);
  print_r("Bitcoin wallet address is :".$asset_info["public_key"]."\n");
  print_r("Bitcoin wallet balance is :".$asset_info["balance"]."\n");
```

#### Baca buku pesanan dari Ocean.one
Bagaimana cara memeriksa harga koin? Anda perlu memahami apa koin dasar. Jika Anda ingin membeli Bitcoin dan menjual USDT, USDT adalah koin dasar. Jika Anda ingin membeli USDT dan menjual Bitcoin, Bitcoin adalah koin dasar.


```php
if ( $ocmd == '1') { getOceanOneMarketInfos(XIN_ASSET_ID,USDT_ASSET_ID);}
function getOceanOneMarketInfos($targetCoin, $baseCoin)  {
  $client = new GuzzleHttp\Client();
  $baseUrl = "https://events.ocean.one/markets/".$targetCoin."-".$baseCoin."/book";
  $res = $client->request('GET', $baseUrl, [
      ]);
  if ($res->getStatusCode() == "200") {
    // echo $res->getStatusCode() . PHP_EOL;
    $resInfo = json_decode($res->getBody(), true);
    echo "Side | Price | Amount | Funds" . PHP_EOL;
    foreach ($resInfo["data"]["data"]["asks"] as $key => $exchange) {
      echo $exchange["side"] . " " . $exchange["price"] . " " . $exchange["amount"] ." " . $exchange["funds"] . PHP_EOL;
    }
    foreach ($resInfo["data"]["data"]["bids"] as $key => $exchange) {
      echo $exchange["side"] . " " . $exchange["price"] . " " . $exchange["amount"] ." " . $exchange["funds"] . PHP_EOL;
    }
  }
}
```

#### Buat memo untuk menyiapkan pesanan
Bab 2: [Echo Bitcoin](https://github.com/wenewzhang/mixin_labs-php-bot/blob/master/README2.md) memperkenalkan koin transfer. Tetapi Anda harus memberi tahu Ocean.one. Seseorang tahu koin mana yang ingin Anda beli.
- **sisi** "B" atau "A", "B" untuk beli, "A" untuk jual.
- **aset** UUID dari aset yang ingin Anda beli
- **harga** Jika Sisi adalah "B", Harga adalah Aset UUID; jika Sidi adalah  "A", Harga adalah aset yang ditransfer ke Ocean.one.

```php
function GenerateOrderMemo($side, $asset, $price) :string {
  $memo = base64_encode(MessagePack::pack([
                       'S' => $side,
                       'A' => Uuid::fromString($asset)->getBytes(),
                       'P' => $price,
                       'T' => 'L',
                       ]));
  return $memo;
}
```

#### Bayar BTC ke OceanOne dengan memo yang dihasilkan
Transfer Bitcoin (BTC_ASSET_ID) ke Ocean.one (OCEANONE_BOT), membuat Anda menargetkan aset uuid (USDT) dalam memo itu.

```php
const OCEANONE_BOT     = "aaff5bef-42fb-4c9f-90e0-29f69176b7d4";
const USDT_ASSET_ID    = "815b0b1a-2764-3736-8faa-42d694fa620a";
const XIN_ASSET_ID     = "c94ac88f-4671-3976-b60a-09064f1811e8";

if ( $ocmd == 's1') {
  $p = readline("Input the Price of XIN/USDT: ");
  $a = readline("Input the Amount of XIN: ");
  $tMemo = GenerateOrderMemo("A",USDT_ASSET_ID,$p);
  echo $tMemo .  PHP_EOL;
  $mixinSdk_WalletInstance = GenerateWalletSDKFromCSV();
  $asset_info = $mixinSdk_WalletInstance->Wallet()->readAsset(XIN_ASSET_ID);
  print_r($asset_info);
  if ( (float) $asset_info["balance"] >= (float) $a ) {
    $transInfos = $mixinSdk_WalletInstance->Wallet()->transfer(XIN_ASSET_ID,OCEANONE_BOT,
                                                $mixinSdk_WalletInstance->getConfig()['default']['pin'],
                                                $a,
                                                $tMemo);
    print_r($transInfos);
    echo "The Order ID (trace_id) is: " . $transInfos["trace_id"] . PHP_EOL;
  } else { echo "Not enough XIN!\n";}
}
```

Jika Anda ingin membeli XIN, sebut saja seperti di bawah ini:

```php
if ( $ocmd == 'b1') {
  $p = readline("Input the Price of XIN/USDT: ");
  $a = readline("Input the Amount of USDT: ");
  $tMemo = GenerateOrderMemo("B",XIN_ASSET_ID,$p);
  echo $tMemo .  PHP_EOL;
  $mixinSdk_WalletInstance = GenerateWalletSDKFromCSV();
  $asset_info = $mixinSdk_WalletInstance->Wallet()->readAsset(USDT_ASSET_ID);

  print_r($asset_info);
  if ( ((float) $asset_info["balance"] >= 1) && ( (float) $asset_info["balance"] >= (float) $a ) ) {
    $transInfos = $mixinSdk_WalletInstance->Wallet()->transfer(USDT_ASSET_ID,OCEANONE_BOT,
                                                $mixinSdk_WalletInstance->getConfig()['default']['pin'],
                                                $a,
                                                $tMemo);
    print_r($transInfos);
    echo "The Order ID (trace_id) is: " . $transInfos["trace_id"] . PHP_EOL;
  } else { echo "Not enough USDT!\n";}
}
```

Output pesanan sukses seperti di bawah ini:
```bash
Input the Price of XIN/USDT: 112
Input the Amount of USDT: 1
hKFToUKhQcQQyUrIj0ZxOXa2CgkGTxgR6KFQozExMqFUoUw=
client id is:26b20aa5-40c0-3e00-9de0-666cfb6f2daa
Array
(
    [type] => asset
    [asset_id] => 815b0b1a-2764-3736-8faa-42d694fa620a
    [chain_id] => c6d0c728-2624-429b-8e0d-d9d19b6592fa
    [symbol] => USDT
    [name] => Tether USD
    [icon_url] => https://images.mixin.one/ndNBEpObYs7450U08oAOMnSEPzN66SL8Mh-f2pPWBDeWaKbXTPUIdrZph7yj8Z93Rl8uZ16m7Qjz-E-9JFKSsJ-F=s128
    [balance] => 1
    [public_key] => 17z1Rq3VsyvvXvGWiHT8YErjBoFgnhErB8
    [account_name] =>
    [account_tag] =>
    [price_btc] => 0.00019038
    [price_usd] => 1.00036293
    [change_btc] => 0.013486479778200063
    [change_usd] => 0.005376748815937048
    [asset_key] => 815b0b1a-2764-3736-8faa-42d694fa620a
    [confirmations] => 6
    [capitalization] => 0
)
Array
(
    [type] => transfer
    [snapshot_id] => f4b1f8d6-004a-4d2b-997d-4d0acf1096cd
    [opponent_id] => aaff5bef-42fb-4c9f-90e0-29f69176b7d4
    [asset_id] => 815b0b1a-2764-3736-8faa-42d694fa620a
    [amount] => -1
    [trace_id] => b12eed67-6cf4-481f-b25b-dd41f28e1984
    [memo] => hKFToUKhQcQQyUrIj0ZxOXa2CgkGTxgR6KFQozExMqFUoUw=
    [created_at] => 2019-04-30T01:17:02.206240549Z
    [counter_user_id] => aaff5bef-42fb-4c9f-90e0-29f69176b7d4
)
The Order ID (trace_id) is: b12eed67-6cf4-481f-b25b-dd41f28e1984
```
## Batalkan Pesanan
Untuk membatalkan pesanan, cukup bayar jumlah aset apa pun ke OceanOne, dan tulis trace_id ke memo. Ocean.one menggunakan trace_id sebagai id pesanan, misalnya, **b12eed67-6cf4-481f-b25b-dd41f28e1984** adalah id pesanan,
Kita bisa menggunakannya untuk membatalkan pesanan.

```php
if ( $ocmd == 'c' ) {
  $mixinSdk_WalletInstance = GenerateWalletSDKFromCSV();
  $asset_info = $mixinSdk_WalletInstance->Wallet()->readAsset(CNB_ASSET_ID);
  if ( ((float) $asset_info["balance"] == 0) ) {
     echo "Please deposit some CNB to this Wallet!" . PHP_EOL;
  } else {
     $orderid = readline("Input the Order id ( trace_id ): ");
     $cMemo =  base64_encode(MessagePack::pack([
                           'O' => Uuid::fromString($orderid)->getBytes(),
                           ]));
     $transInfos = $mixinSdk_WalletInstance->Wallet()->transfer(CNB_ASSET_ID,OCEANONE_BOT,
                                                 $mixinSdk_WalletInstance->getConfig()['default']['pin'],
                                                 "0.00000001",
                                                 $cMemo);
     print_r($transInfos);
  }
}
```
#### Lihat saldo Bitcoin
Periksa saldo dompet.
```php
if ($line == 'aw') {
  $mixinSdk_eachAccountInstance = GenerateWalletSDKFromCSV();
  $asset_info = $mixinSdk_eachAccountInstance->Wallet()->readAssets();
  foreach ($asset_info as $key => $asset) {
    echo  $asset["symbol"] . "   " . $asset["asset_id"] ."   ". $asset["balance"] .
          "   ". $asset["public_key"].PHP_EOL;
  }
}
```

## Penggunaan kode sumber
Bangun dan jalankan.

- **php bitcoin_wallet.php** jalankan itu.

Perintah perdagangan dengan OceanOne:

- o: Pertukaran Ocean.One
- q: Keluar

Tentukan pilihan Anda (mis: q untuk Keluar!):

- 1:  Ambil pesanan XIN/USDT
- s1: Jual XIN/USDT
- b1: Beli/USDT
- 2:  Ambil pesanan ERC20 (Benz) / USDT
- s2: Jual Benz/USDT
- b2: Beli Benz/USDT
- q:  Keluar

[Kode lengkap disini](https://github.com/wenewzhang/mixin_labs-php-bot/blob/master/bitcoin_wallet.php)

