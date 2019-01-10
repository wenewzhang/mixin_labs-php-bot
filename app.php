// Configuration file format
<?php

// include "vendor/exinone/mixin-sdk-php/src/MixinSDK.php";
// use ExinOne\MixinSDK;
require 'vendor/autoload.php';
use ExinOne\MixinSDK\MixinSDK;
// use MixinSDK;
$config0 = [
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


$mixinSdk = new MixinSDK();
// use setConfig method to save config
$mixinSdk->setConfig('myConfig-A',$config0);
// $mixinSdk->setConfig('myConfig-B',$config1);
// then you can
$BotInfo = $mixinSdk->use('myConfig-A')->user()->readProfile();
print_r($BotInfo);
//-------
// Or more simple way, using the 'use' method , chained with other methods
// $mixinSdk->use('myConfig-A',$config)->user()->readProfile();
// then you can
// $mixinSdk->use('myConfig-A')->user()->readProfile();
