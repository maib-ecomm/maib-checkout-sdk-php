<?php
require __DIR__  . '/config.php';

$baseUrl = "https://sandbox.maibmerchants.md/v2/"; // optional, if is not provided, the production url will be used by default

// Get Access Token with Client ID and Client Secret
$auth = MaibCheckoutAuthRequest::create($baseUrl)->generateToken(CLIENT_ID, CLIENT_SECRET);

$token = $auth->accessToken;

$data = [
    'amount' => 22.50, // required
    'currency' => 'MDL', // required
    'callbackUrl' => 'https://example.com/callabck',
    'failUrl' => 'https://example.com/fail',
    'successUrl' => 'https://example.com/success',
    'language' => 'ro',
    'payerInfo' => [
        'name' => 'jonn smith',
        'phone' => '37369222222',
        'email' => 'test.email@gmail.com',
        'useragent' => 'chrome',
        'ip' => '192.193.245.11',
    ],
    'orderInfo' => [
        'orderAmount' => 19.52,
        'orderCurrency' => 'MDL',
        'deliveryAmount' => 21.43,
        'deliveryCurrency' => 'mdl',
        'description' => 'order description',
        'id' => 'ty223423',
        'items' => [
            [
                'externalId' => '234214DFS',
                'title' => 'item1',
                'currency' => 'MDL',
                'amount' => 10.23,
                'quantity' => 1,
                'displayOrder' => 1,
                'id' => '32423422',
            ],
            [
                'externalId' => '3333452DFS',
                'title' => 'item2',
                'currency' => 'MDL',
                'amount' => 11.25,
                'quantity' => 2,
                'displayOrder' => 3,
                'id' => '453434345',
            ],
        ],
    ],
];



// Create checkout request
$createCheckoutResponse = MaibCheckoutApiRequest::create($baseUrl)->createCheckout($data, $token);

if (isset($createCheckoutResponse->ok) && $createCheckoutResponse->ok){
    $checkoutId = $createCheckoutResponse->result->checkoutId;
    $checkoutUrl = $createCheckoutResponse->result->checkoutUrl; // url to which payer must be redirected to
}
else{
    foreach ($createCheckoutResponse->errors as $error) {
        echo $error->errorCode . ": " . $error->errorMessage;
    }
}
