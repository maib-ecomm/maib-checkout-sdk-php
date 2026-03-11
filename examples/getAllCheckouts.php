<?php
require __DIR__  . '/config.php';

$baseUrl = "https://sandbox.maibmerchants.md/v2/"; // optional, if is not provided, the production url will be used by default

// Get Access Token with Client ID and Client Secret
$auth = MaibCheckoutAuthRequest::create($baseUrl)->generateToken(CLIENT_ID, CLIENT_SECRET);

$token = $auth->accessToken;

// all filter parameters are optional
$filters = [
    'id' => '7b5b1b8b-3a2b-4e33-9f7d-0a7a9d6b4f88',
    'orderId' => 'ORD-2026-000123',
    'status' => 'Initialized',
    'minAmount' => 10.50,
    'maxAmount' => 250.00,
    'currency' => 'MDL',
    'language' => 'ro',
    'payerName' => 'John Smith',
    'payerEmail' => 'test.email@gmail.com',
    'payerPhone' => '37369222222',
    'payerIp' => '192.168.100.25',
    'createdAtFrom' => '2026-02-01T00:00:00+02:00',
    'createdAtTo' => '2026-02-12T23:59:59+02:00',
    'expiresAtFrom' => '2026-02-12T00:00:00+02:00',
    'expiresAtTo' => '2026-02-20T23:59:59+02:00',
    'cancelledAtFrom' => '2026-02-05T00:00:00+02:00',
    'cancelledAtTo' => '2026-02-06T23:59:59+02:00',
    'failedAtFrom' => '2026-02-07T00:00:00+02:00',
    'failedAtTo' => '2026-02-08T23:59:59+02:00',
    'completedAtFrom' => '2026-02-09T00:00:00+02:00',
    'completedAtTo' => '2026-02-10T23:59:59+02:00',
    'count' => 25,
    'offset' => 0,
    'sortBy' => 'CreatedAt',
    'order' => 'Desc',
];


// Get all checkouts request
$checkoutsResponse = MaibCheckoutApiRequest::create($baseUrl)->getAllCheckouts($token, $filters); // filter is optional, if is not provided all checkouts will be returned

if (isset($checkoutsResponse->ok) && $checkoutsResponse->ok){
    $count = $checkoutsResponse->result->count;
    $totalCount = $checkoutsResponse->result->totalCount;
    $checkouts = $checkoutsResponse->result->items;
}
else{
    foreach ($checkoutsResponse->errors as $error) {
        echo $error->errorCode . ": " . $error->errorMessage;
    }
}
