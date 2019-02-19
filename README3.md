In last two chapters, we create a bot to [receive user's message and send message back to user](https://github.com/wenewzhang/mixin_labs-php-bot/blob/master/README.md), the bot can [receive Bitcoin and send it back to user](https://github.com/wenewzhang/mixin_labs-php-bot/blob/master/README2.md). Next, we will show you how to create a Bitcoin wallet, read and send Bitcoin by PHP based on Mixin Network API.

# How to xxx Bitcoin by PHP
## Create Bitcoin wallet by PHP
#### Create a Mixin Network account to acquire Bitcoin wallet
Creating a Bitcoin wallet is very easy on Mixin Network. Developer just need to prepare an unique RSA key pair and call Mixin Network API to create a Mixin Network account. The account not only support Bitcoin wallet, but also support Ethereum, EOS, Etc. Full blockchain support list is [here](https://mixin.one/network/chains). All ERC20 Token and EOS token are also supported by the account.
```
```

#### Read Bitcoin asset balance and deposit address
Now we can read the Bitcoin asset by Deposit/Read Asset API after we created an account.
```
```
The address of Bitcoin is not generated automatically at same time when the account is created, it is created when user read Bitcoin asset for the first time.

You can found information about Bitcoin asset in the account. Public address is the deposit address for you. 
```
```
You may ask where is Bitcoin private key? The private key is protected by multi signature from Mixin Network. Bitcoin Asset can only be withdraw to other address when user provide correct RSA private key signature, PIN code and Session key. This design

## Deposit some Bitcoin into the address from other exchange or wallet
Now you can deposit some bitcoin from other exchange or wallet. This is maybe too expensive for our tutorial. So you can use your own Mixin messenger user account to transfer super tiny bitcoin to the account you just created. It is free and confirmed instantly.

Transfer Bitcoin to the account is very easy. You just need to prepare a URL like:

```
```

![send]()
Now you can read Bitcoin balance of the account again to confirm the action.
```
```
## Instantly send Bitcoin to another Mixin Network account with zero cost
let's transfer the Bitcoin back to your Mixin Messenger user account for free and instantly.
```
```
![Confirm the result in Mixin messenger]()

## Send Bitcoin to another Bitcoin address like normal Bitcoin transaction
Now let's withdraw Bitcoin to other exchange or wallet. 
#### Create withdraw address
We need to add a Bitcoin withdrawal address by call [API](), the ID of address will be returned in result of API.
```
```
#### Withdraw Bitcoin
Now we can perform a withdraw action by call API.
```
```
The API result will give you the Bitcoin transaction ID.

#### Confirm the transction in blockchain explore


EOS|6cfe566e-4aad-470b-8c9a-2fd35b49c68d
CNB|965e5c6e-434c-3fa9-b780-c50f43cd955c
BTC|c6d0c728-2624-429b-8e0d-d9d19b6592fa
ETC|2204c1ee-0ea2-4add-bb9a-b3719cfff93a
XRP|23dfb5a5-5d7b-48b6-905f-3970e3176e27
XEM|27921032-f73e-434e-955f-43d55672ee31
ETH|43d61dcd-e413-450d-80b8-101d5e903357
DASH|6472e7e3-75fd-48b6-b1dc-28d294ee1476
DOGE|6770a1e5-6086-44d5-b60f-545f9d9e8ffd
LTC|76c802a2-7c88-447f-a93e-c29c9e5dd9c8
SC|990c4c29-57e9-48f6-9819-7d986ea44985
ZEN|a2c5d22b-62a2-4c13-b3f0-013290dbac60
ZEC|c996abc9-d94e-4494-b1cf-2a3fd3ac5714
BCH|fd11b6e3-0b87-41f1-a41f-f0e9b49e5bf0
