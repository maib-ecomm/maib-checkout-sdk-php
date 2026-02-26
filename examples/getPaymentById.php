<?php
require __DIR__  . '/config.php';

$baseUrl = "https://sandbox.maibmerchants.md/v2/"; // optional, if is not provided, the production url will be used by default

// Get Access Token with Client ID and Client Secret
$auth = MaibCheckoutAuthRequest::create($baseUrl)->generateToken(CLIENT_ID, CLIENT_SECRET);

$token = $auth->accessToken;

$paymentId = 'g45b2d61-5739-4425-9ebb-7861002e8b10';

// Payment refund request
$payment = MaibCheckoutApiRequest::create($baseUrl)->getPayment($paymentId, $token);

// Display request response
$jsonData = json_encode($payment);
echo $jsonData;
