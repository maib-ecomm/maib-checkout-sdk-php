[![N|Solid](https://www.maib.md/images/logo.svg)](https://www.maib.md)

# PHP SDK for **maib** Checkout API

**maib** Checkout API docs: [https://docs.maibmerchants.md/checkout](https://docs.maibmerchants.md/checkout)

---

## Requirements

- PHP >= 5.6  
- Required PHP extensions:
  - curl
  - json

---

## Install

Download the latest release and include SDK classes manually:

```php
require __DIR__ . '/../src/MaibCheckoutAuthRequest.php';
require __DIR__ . '/../src/MaibCheckoutApiRequest.php';
require __DIR__ . '/../src/MaibCheckoutSdk.php';
```

---

## Getting started

### Import classes

```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

use MaibEcomm\MaibCheckoutSdk\MaibCheckoutAuthRequest;
use MaibEcomm\MaibCheckoutSdk\MaibCheckoutApiRequest;
```

### Configuration

```php
const CLIENT_ID = 'YOUR_CLIENT_ID';
const CLIENT_SECRET = 'YOUR_CLIENT_SECRET';
const SIGNATURE_KEY = 'YOUR_SIGNATURE_SECRET';

// Example base URL (optional). Production url is used by default:
$baseUrl = 'YOUR_BASE_URL';
```

> `CLIENT_ID`, `CLIENT_SECRET` and `SIGNATURE_KEY` are available after payment profile activation.

---

## Authentication (Bearer Token)

Before calling Checkout API endpoints you must obtain a **Bearer token**:

```php
$auth = MaibCheckoutAuthRequest::create($baseUrl)->generateToken(CLIENT_ID, CLIENT_SECRET);
$token = $auth->accessToken;
```

---

## API usage examples

### 1) Create Checkout (`createCheckout`)

```php
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

// Get token
$auth = MaibCheckoutAuthRequest::create($baseUrl)->generateToken(CLIENT_ID, CLIENT_SECRET);
$token = $auth->accessToken;

// Create checkout request
$createCheckoutResult = MaibCheckoutApiRequest::create($baseUrl)->createCheckout($data, $token);

// Display response
echo json_encode($createCheckoutResult);

// Useful fields
$checkoutId = $createCheckoutResult->checkoutId;
$checkoutUrl = $createCheckoutResult->checkoutUrl; // redirect payer to this URL
```

---

### 2) Refund (`refund`)

```php
$data = [
    'payId' => '24dd3ac9-2535-4590-8eff-082eb5f20200', // required (paymentId from callback)
    'amount' => 12.55, // optional (if not provided -> full refund)
    'reason' => 'Some reason for refund operation' // optional
];

// Get token
$auth = MaibCheckoutAuthRequest::create($baseUrl)->generateToken(CLIENT_ID, CLIENT_SECRET);
$token = $auth->accessToken;

// Refund request
$paymentRefundResult = MaibCheckoutApiRequest::create($baseUrl)->refund($data, $token);

// Display response
echo json_encode($paymentRefundResult);

// Useful fields
$refundId = $paymentRefundResult->refundId;
$status = $paymentRefundResult->status; // 'Created' means success response
```

---

### 3) Get Checkout by ID (`getCheckout`)

```php
$checkoutId = '5c95c821-3aa2-486b-9b64-d62e9c7f8b5d';

// Get token
$auth = MaibCheckoutAuthRequest::create($baseUrl)->generateToken(CLIENT_ID, CLIENT_SECRET);
$token = $auth->accessToken;

// Get checkout
$checkout = MaibCheckoutApiRequest::create($baseUrl)->getCheckout($checkoutId, $token);

// Display response
echo json_encode($checkout);
```

---

### 4) Get all Checkouts by filter (`getAllCheckouts`)

> Filters are optional. If `$filters` is not provided, all checkouts will be returned.

```php
$filters = [
    'id' => '7b5b1b8b-3a2b-4e33-9f7d-0a7a9d6b4f21',
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

// Get token
$auth = MaibCheckoutAuthRequest::create($baseUrl)->generateToken(CLIENT_ID, CLIENT_SECRET);
$token = $auth->accessToken;

// Get all checkouts
$checkoutsResult = MaibCheckoutApiRequest::create($baseUrl)->getAllCheckouts($token, $filters);

// Display response
echo json_encode($checkoutsResult);

// Useful fields
$count = $checkoutsResult->count;
$totalCount = $checkoutsResult->totalCount;
$checkouts = $checkoutsResult->items; // array of checkout objects
```

---

## Callback URL: signature verification (example)

To validate notification signature you need `SIGNATURE_KEY`.

Signature rules:
- HMAC-SHA256 over: `{rawBody}.{timestamp}`
- `X-Signature` header format: `sha256=<base64>`
- `X-Signature-Timestamp` is Unix epoch timestamp in **milliseconds**
- Compare using constant-time comparison (`hash_equals`)

Minimal verification example:

```php
$signatureKey = SIGNATURE_KEY;

$rawBody = file_get_contents('php://input');

$headers = getallheaders();
$xSignature = $headers['X-Signature'];                 // "sha256=<base64>"
$xTimestamp = $headers['X-Signature-Timestamp'];       // unix epoch ms

$receivedSig = substr($xSignature, 7);                 // remove "sha256="

$message = $rawBody . '.' . $xTimestamp;

$expectedSig = base64_encode(hash_hmac('sha256', $message, $signatureKey, true));

$isValid = hash_equals($expectedSig, $receivedSig);

if (!$isValid) {
    http_response_code(401);
    exit('Invalid signature');
}

http_response_code(200);
echo 'OK';
```

---

## Notes

- Always use the **raw** JSON body for signature verification (do not re-encode JSON).
- Token must be obtained before calling Checkout API endpoints.

## Enumerations

### Currencies (case insensitive)
- `MDL`
- `EUR`
- `USD`

### Languages (case insensitive)
- `EN`
- `RU`
- `RO`

### Order options (case insensitive)
- `ASC`
- `DESC`

### Sort fields (case sensitive)
- `CreatedAt`
- `Amount`
- `Status`
- `ExpiresAt`
- `FailedAt`
- `CancelledAt`
- `CompletedAt`

### Checkout statuses (case sensitive)
- `WaitingForInit`
- `Initialized`
- `PaymentMethodSelected`
- `Completed`
- `Expired`
- `Abandoned`
- `Cancelled`
- `Failed`

