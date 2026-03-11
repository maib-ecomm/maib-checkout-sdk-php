<?php
require __DIR__  . '/config.php';

$baseUrl = "https://sandbox.maibmerchants.md/v2/"; // optional, if is not provided, the production url will be used by default

// Get Access Token with Client ID and Client Secret
$auth = MaibCheckoutAuthRequest::create($baseUrl)->generateToken(CLIENT_ID, CLIENT_SECRET);

$token = $auth->accessToken;

$data = [
    'payId' => '24dd3ac9-2535-4590-8eff-082eb5f20200', // required, id of the payment received in callback
    'amount' => 12.55, // optional, if is not provided, full refund will be performed
    'reason' => 'Some reason for refund operation' // optional
];

// Payment refund request
$paymentRefundResponse = MaibCheckoutApiRequest::create($baseUrl)->refund($data, $token);

if (isset($paymentRefundResponse->ok) && $paymentRefundResponse->ok){
    $refundId = $paymentRefundResponse->result->refundId;
    $status = $paymentRefundResponse->result->status; // 'Created' will be return as successful response
}
else{
    foreach ($paymentRefundResponse->errors as $error) {
        echo $error->errorCode . ": " . $error->errorMessage;
    }
}


