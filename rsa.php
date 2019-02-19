
<?php
// Create the keypair
$res=openssl_pkey_new(array('private_key_bits' => 1024));

// print_r($res);
$ary = array('a'=>"b",'c'=>'d');
if (isset($ary['b'])) {
   print($ary['a']);
 }
// print_r($ary);
// // Get private key
// openssl_pkey_export($res, $privkey);
//
// // Get public key
// $pubkey=openssl_pkey_get_details($res);
// $pubkey=$pubkey["key"];
//
// print_r($privkey);
// print_r($pubkey);
