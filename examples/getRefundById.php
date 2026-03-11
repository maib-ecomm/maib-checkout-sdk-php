<?php
require __DIR__  . '/config.php';

$baseUrl = "https://sandbox.maibmerchants.md/v2/"; // optional, if is not provided, the production url will be used by default

// Get Access Token with Client ID and Client Secret
$auth = MaibCheckoutAuthRequest::create($baseUrl)->generateToken(CLIENT_ID, CLIENT_SECRET);

$token = $auth->accessToken;

$refundId = '8F9C7680-BC46-47F7-AED4-88630ADAFCD1';

// Get refund request
$getRefundResponse = MaibCheckoutApiRequest::create($baseUrl)->getRefund($refundId, $token);

if (isset($getRefundResponse->ok) && $getRefundResponse->ok){
    $jsonData = json_encode($getRefundResponse->result);
    echo $jsonData;
}
else{
    foreach ($getRefundResponse->errors as $error) {
        echo $error->errorCode . ": " . $error->errorMessage;
    }
}
