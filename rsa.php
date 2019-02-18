
<?php
// Create the keypair
$res=openssl_pkey_new(array('private_key_bits' => 1024));

// Get private key
openssl_pkey_export($res, $privkey);

// Get public key
$pubkey=openssl_pkey_get_details($res);
$pubkey=$pubkey["key"];

print_r($privkey);
print_r($pubkey);
