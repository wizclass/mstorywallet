<?php

require 'vendor/autoload.php';

use neto737\BitGoSDK\BitGoSDK;
use neto737\BitGoSDK\BitGoExpress;
use neto737\BitGoSDK\Enum\CurrencyCode;

$hostname = 'localhost';
$port = 3080;
$coin = CurrencyCode::BITCOIN_TESTNET;

$bitgo = new BitGoSDK('YOUR_API_KEY_HERE', $coin, true);

/**
 * To send any transaction with BitGoExpress SDK you need to unlock your wallet
 * If you're not using testnet to send coins, you need to unlock your wallet with
 * your OTP password (2FA)
 */
$bitgo->unlockSession('0000000');

$bitgoExpress = new BitGoExpress($hostname, $port, $coin);
$bitgoExpress->accessToken = 'YOUR_API_KEY_HERE';
$bitgoExpress->walletId = 'YOUR_WALLET_ID_HERE';

/**
 * Send the amount in satoshi
 */
$value_in_btc = 0.25;
$amount = BitGoSDK::toSatoshi($value_in_btc);

$sendTransaction = $bitgoExpress->sendTransaction('DESTINATION_ADDRESS', $amount, 'YOUR_WALLET_PASSPHRASE');
var_dump($sendTransaction);