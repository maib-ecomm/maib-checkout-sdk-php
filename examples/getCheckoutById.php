<?php
require __DIR__  . '/config.php';

$baseUrl = "https://sandbox.maibmerchants.md/v2/"; // optional, if is not provided, the production url will be used by default

// Get Access Token with Client ID and Client Secret
$auth = MaibCheckoutAuthRequest::create($baseUrl)->generateToken(CLIENT_ID, CLIENT_SECRET);

$token = $auth->accessToken;

$checkoutId = '5c99c642-3aa2-486b-9b64-d62e9c7f8b5r';

// Get checkout by id
$checkoutResponse = MaibCheckoutApiRequest::create($baseUrl)->getCheckout($checkoutId, $token);

if (isset($checkoutResponse->ok) && $checkoutResponse->ok){
    $jsonData = json_encode($checkoutResponse->result);
    echo $jsonData;
}
else{
    foreach ($checkoutResponse->errors as $error) {
        echo $error->errorCode . ": " . $error->errorMessage;
    }
}
