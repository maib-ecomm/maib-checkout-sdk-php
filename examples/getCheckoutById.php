<?php
require __DIR__  . '/config.php';

$baseUrl = "https://sandbox.maibmerchants.md/v2/"; // optional, if is not provided, the production url will be used by default

// Get Access Token with Client ID and Client Secret
$auth = MaibCheckoutAuthRequest::create($baseUrl)->generateToken(CLIENT_ID, CLIENT_SECRET);

$token = $auth->accessToken;

$checkoutId = '5c95c821-3aa2-486b-9b64-d62e9c7f8b5d';

// Payment refund request
$checkout = MaibCheckoutApiRequest::create($baseUrl)->getCheckout($checkoutId, $token);

// Display request response
$jsonData = json_encode($checkout);
echo $jsonData;
