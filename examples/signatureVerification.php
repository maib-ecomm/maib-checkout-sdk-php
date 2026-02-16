<?php
require __DIR__ . '/config.php';

$rawBody = file_get_contents('php://input');

$headers = getallheaders();
$xSignature = $headers['X-Signature'];                 // "sha256=<base64>"
$xTimestamp = $headers['X-Signature-Timestamp'];       // unix epoch ms

$receivedSig = substr($xSignature, 7);                 // remove "sha256="

$message = $rawBody . '.' . $xTimestamp;

$expectedSig = base64_encode(hash_hmac('sha256', $message, SIGNATURE_KEY, true));

$isValid = hash_equals($expectedSig, $receivedSig);

if (!$isValid) {
    exit('Invalid signature');
}

echo 'Signature is valid!';
