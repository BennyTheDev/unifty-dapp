<?php
/*
require __DIR__ . '/vendor/autoload.php';

use Elliptic\EC;
use kornrunner\Keccak;
*/

$dsn = 'mysql:host=localhost;dbname=unifty;charset=utf8';
$usr = 'root';
$pwd = '';

/*
$dsn = 'mysql:host=premium163.web-hosting.com;dbname=unifghiu_coindesk;charset=utf8';
$usr = 'unifghiu_coindesk';
$pwd = 'vollgeschissen123';
*/
$pdo = new PDO($dsn, $usr, $pwd);

/*
function pubKeyToAddress($pubkey) {
    return "0x" . substr(Keccak::hash(substr(hex2bin($pubkey->encode("hex")), 1), 256), 24);
}

function verifySignature($message, $signature, $address) {
    $msglen = strlen($message);
    $hash   = Keccak::hash("\x19Ethereum Signed Message:\n{$msglen}{$message}", 256);
    $sign   = ["r" => substr($signature, 2, 64),
        "s" => substr($signature, 66, 64)];
    $recid  = ord(hex2bin(substr($signature, 130, 2))) - 27;
    if ($recid != ($recid & 1))
        return false;

    $ec = new EC('secp256k1');
    $pubkey = $ec->recoverPubKey($hash, $sign, $recid);

    return strtolower($address) == pubKeyToAddress($pubkey);
}*/

header("Content-type:application/json");

if(!isset($_REQUEST['pw']) || $_REQUEST['pw'] != 'schimmelpilz'){

    http_response_code(403);
    die('forbidden');
}

if( !isset($_REQUEST['processed']) ) {

    $stmt = $pdo->prepare("SELECT * FROM `coindeskemails` WHERE `address` <> '' And `signature` <> '' And `processed` = 0 Order By id Asc Limit 1");
    $stmt->execute([]);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($result);

}
else{

    $stmt = $pdo->prepare("Update `coindeskemails` Set `processed` = 1 WHERE `id` = ?");
    $stmt->execute([intval($_REQUEST['processed'])]);
}

// address to be assumed to be a checksum address already
//$address   = "0x3f3Effe7578870E686cf334A06E19d816DdF1d6B";
//$message   = "Dicker eier";
// signature returned by eth.sign(address, message)
//$signature = "0xc44479c6e0be992f966ee42e7d96cbc1198cfc8c981e7c4cadbc4f00c4533f4957994d0f16dea6b9b059d8cdc8820b5cd5e9700f1e60cc6cf2a3f657f19d3fa71c";

/*
if (verifySignature($message, $signature, $address)) {
    echo "Success\n";
} else {
    echo "Fail\n";
}*/