<?php
require __DIR__ . '/../src/MaibCheckoutAuthRequest.php';
require __DIR__ . '/../src/MaibCheckoutApiRequest.php';
require __DIR__ . '/../src/MaibCheckoutSdk.php';

class_alias("MaibEcomm\MaibCheckoutSdk\MaibCheckoutAuthRequest", "MaibCheckoutAuthRequest");
class_alias("MaibEcomm\MaibCheckoutSdk\MaibCheckoutApiRequest", "MaibCheckoutApiRequest");

// Client Secret, Client ID and Signature Key are available after Profile activation
const CLIENT_ID = 'YOUR_CLIENT_ID';
const CLIENT_SECRET = 'YOUR_CLIENT_SECRET';
const SIGNATURE_KEY = 'YOUR_SIGNATURE_SECRET';
