<?php
/**
 * PHP SDK for maib Checkout API
 *
 * @package maib-ecomm/maib-checkout-sdk-php
 * @category SDK
 * @author maib
 * @license MIT
 */
namespace MaibEcomm\MaibCheckoutSdk;

use RuntimeException;

class RequestValidationException extends RuntimeException {}

class ResponseException extends RuntimeException {}

// Factory class responsible for creating new instances of the MaibAuth class.
class MaibCheckoutApiRequest
{
    /**
     * Creates a new instance of MaibCheckoutApi.
     *
     * @return MaibCheckoutApi
     */
    public static function create($baseUrl = null)
    {
        // Create a new instance of the MaibCheckoutSdk class and pass it to the MaibCheckoutAuth constructor.
        $httpClient = new MaibCheckoutSdk($baseUrl);
        return new MaibCheckoutApi($httpClient);
    }
}

class MaibCheckoutApi
{
    private $httpClient;
    
    /**
     * Constructs a new MaibCheckoutApi instance.
     *
     * @param MaibCheckoutSdk $httpClient The HTTP client for sending requests.
     */
    public function __construct(MaibCheckoutSdk $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * Sends a request to the pay endpoint.
     *
     * @param array $data The parameters for the request.
     * @param string $token The authentication token.
     * @throws RequestValidationException if the request fails.
     */
    public function createCheckout($data, $token)
    {
        $requiredParams = ['amount', 'currency'];
        try
        {
            self::validateCreateCheckoutParams($data, $requiredParams);
            self::validateAccessToken($token);

            if ($data["amount"]) {
                $data["amount"] = round($data["amount"], 2);
            }

            return $this->sendRequestPost(MaibCheckoutSdk::CREATE_CHECKOUT, $data, $token);
        }
        catch(RequestValidationException $e)
        {
            error_log('Invalid request: ' . $e->getMessage());
            throw new RequestValidationException('Invalid request: ' . $e->getMessage());
        }
    }

    /**
     * Sends a request to the refund endpoint.
     *
     * @param array $data The parameters for the request.
     * @param string $token The authentication token.
     * @throws RequestValidationException if the request fails.
     */
    public function refund($data, $token)
    {
        $requiredParams = ['payId'];
        try
        {
            self::validateRefundParams($data, $requiredParams);
            self::validateAccessToken($token);

            if (isset($data['amount'])) {
                $data["amount"] = round($data["amount"], 2);
            }

            $endpoint = str_replace("{Id}", $data['payId'], MaibCheckoutSdk::REFUND);
            unset($data['payId']);

            return $this->sendRequestPost($endpoint, $data, $token);
        }
        catch(RequestValidationException $e)
        {
            error_log('Invalid request: ' . $e->getMessage());
            throw new RequestValidationException('Invalid request: ' . $e->getMessage());
        }
    }

    /**
     * Sends a request to the get checkout by id endpoint.
     *
     * @param string $id The parameters for the request.
     * @param string $token The authentication token.
     * @throws RequestValidationException if the request fails.
     */
    public function getCheckout($id, $token)
    {
        try
        {
            self::validateIdParam($id);
            self::validateAccessToken($token);

            $endpoint = str_replace("{Id}", $id, MaibCheckoutSdk::GET_CHECKOUT);

            return $this->sendRequestGet($endpoint, $token);
        }
        catch(RequestValidationException $e)
        {
            error_log('Invalid request: ' . $e->getMessage());
            throw new RequestValidationException('Invalid request: ' . $e->getMessage());
        }
    }

    /**
     * Sends a request to the get payment by id endpoint.
     *
     * @param string $id The parameters for the request.
     * @param string $token The authentication token.
     * @throws RequestValidationException if the request fails.
     */
    public function getPayment($id, $token)
    {
        try
        {
            self::validateIdParam($id);
            self::validateAccessToken($token);

            $endpoint = str_replace("{Id}", $id, MaibCheckoutSdk::GET_PAYMENT);

            return $this->sendRequestGet($endpoint, $token);
        }
        catch(RequestValidationException $e)
        {
            error_log('Invalid request: ' . $e->getMessage());
            throw new RequestValidationException('Invalid request: ' . $e->getMessage());
        }
    }

    /**
     * Sends a request to the get refund by id endpoint.
     *
     * @param string $id The parameters for the request.
     * @param string $token The authentication token.
     * @throws RequestValidationException if the request fails.
     */
    public function getRefund($id, $token)
    {
        try
        {
            self::validateIdParam($id);
            self::validateAccessToken($token);

            $endpoint = str_replace("{Id}", $id, MaibCheckoutSdk::GET_REFUND);

            return $this->sendRequestGet($endpoint, $token);
        }
        catch(RequestValidationException $e)
        {
            error_log('Invalid request: ' . $e->getMessage());
            throw new RequestValidationException('Invalid request: ' . $e->getMessage());
        }
    }

    /**
     * Sends a request to the get all checkouts by filter endpoint.
     *
     * @param string $filter The filter parameter for the request.
     * @param string $token The authentication token.
     * @throws RequestValidationException if the request fails.
     */
    public function getAllCheckouts($token, $filter = [])
    {
        try
        {
            self::validateCheckoutFilter($filter);
            self::validateAccessToken($token);

            return $this->sendRequestGet(MaibCheckoutSdk::GET_ALL_CHECKOUTS, $token, $filter);
        }
        catch(RequestValidationException $e)
        {
            error_log('Invalid request: ' . $e->getMessage());
            throw new RequestValidationException('Invalid request: ' . $e->getMessage());
        }
    }

    /**
     * Sends a POST request to the specified endpoint.
     *
     * @param string $endpoint The endpoint to send the request to.
     * @param array $data The parameters for the request.
     * @param string $token The authentication token.
     * @throws RequestValidationException if the request fails.
     * @return mixed The response from the API.
     */
    private function sendRequestPost($endpoint, $data, $token)
    {
        try
        {
            $response = $this
                ->httpClient
                ->post($endpoint, $data, $token);
        }
        catch(\Exception $e)
        {
            throw new ResponseException("HTTP error while sending POST request to endpoint $endpoint: {$e->getMessage() }");
        }
        return $response;
    }
    
    /**
     * Sends a GET request to the specified endpoint.
     *
     * @param string $endpoint The endpoint to send the request to.
     * @param string $token The authentication token.
     * @param array $params The query parameters.
     * @return mixed The response from the API.
     *@throws RequestValidationException if the request fails.
     */
    private function sendRequestGet($endpoint, $token, $params = [])
    {
        try
        {
            $response = $this
                ->httpClient
                ->get($endpoint, $token, $params);
        }
        catch(\Exception $e)
        {
            throw new ResponseException("HTTP error while sending GET request to endpoint $endpoint: {$e->getMessage() }");
        }
        return $response;
    }

    private static function validateAccessToken($token)
    {
        if (!is_string($token) || empty($token))
        {
            throw new RequestValidationException("Access token is not valid. It should be a non-empty string.");
        }
    }

    private static function validateIdParam($id)
    {
        if (!isset($id))
        {
            throw new RequestValidationException("Missing ID!");
        }
        if (!is_string($id) || strlen($id) !== 36)
        {
            throw new RequestValidationException("Invalid 'ID' parameter. Should be string of 36 characters.");
        }
    }

    private static function validateCreateCheckoutParams($data, $requiredParams)
    {
        foreach ($requiredParams as $param) {
            if (!isset($data[$param])) {
                throw new RequestValidationException("Missing required parameter: '$param'");
            }
        }

        if (!is_numeric($data['amount']) || $data['amount'] <= 0.01) {
            throw new RequestValidationException("Invalid 'amount' parameter. Should be a numeric value > 0.");
        }

        if (!in_array(strtoupper((string)$data['currency']), MaibCheckoutSdk::CURRENCIES, true)) {
            throw new RequestValidationException("Invalid 'currency' parameter. Currency should be one of 'MDL', 'EUR', or 'USD'.");
        }

        if (isset($data['language']) && !in_array(strtoupper((string)$data['language']), MaibCheckoutSdk::LANGUAGES, true)) {
            throw new RequestValidationException("Invalid 'language' parameter. Allowed values: 'RO', 'EN', 'RU'.");
        }

        self::validateUrlIfPresent($data, 'callbackUrl');
        self::validateUrlIfPresent($data, 'successUrl');
        self::validateUrlIfPresent($data, 'failUrl');

        if (isset($data['payerInfo'])) {
            if (!is_array($data['payerInfo'])) {
                throw new RequestValidationException("Invalid 'payerInfo' parameter. Should be an object.");
            }

            $payer = $data['payerInfo'];

            if (isset($payer['name']) && (!is_string($payer['name']) || trim($payer['name']) === '')) {
                throw new RequestValidationException("Invalid 'payerInfo.name' parameter. Should be a non-empty string.");
            }
            if (isset($payer['email']) && (!is_string($payer['email']) || !filter_var($payer['email'], FILTER_VALIDATE_EMAIL))) {
                throw new RequestValidationException("Invalid 'payerInfo.email' parameter. Please provide a valid email address.");
            }
            if (isset($payer['phone']) && (!is_string($payer['phone']) || trim($payer['phone']) === '')) {
                throw new RequestValidationException("Invalid 'payerInfo.phone' parameter. Should be a non-empty string.");
            }
            if (isset($payer['ip']) && (!is_string($payer['ip']) || !filter_var($payer['ip'], FILTER_VALIDATE_IP))) {
                throw new RequestValidationException("Invalid 'payerInfo.ip' parameter. Please provide a valid IP address.");
            }
            if (isset($payer['userAgent']) && (!is_string($payer['userAgent']) || trim($payer['userAgent']) === '')) {
                throw new RequestValidationException("Invalid 'payerInfo.userAgent' parameter. Should be a non-empty string.");
            }
        }

        if (isset($data['orderInfo'])) {
            if (!is_array($data['orderInfo'])) {
                throw new RequestValidationException("Invalid 'orderInfo' parameter. Should be an object.");
            }

            $order = $data['orderInfo'];

            if (isset($order['id']) && (!is_string($order['id']) || trim($order['id']) === '')) {
                throw new RequestValidationException("Invalid 'orderInfo.id' parameter. Should be a non-empty string.");
            }
            if (isset($order['description']) && (!is_string($order['description']) || trim($order['description']) === '')) {
                throw new RequestValidationException("Invalid 'orderInfo.description' parameter. Should be a non-empty string.");
            }

            if (isset($order['date'])) {
                if (!is_string($order['date']) || trim($order['date']) === '') {
                    throw new RequestValidationException("Invalid 'orderInfo.date' parameter. Should be an ISO8601 datetime string.");
                }
                try {
                    new \DateTimeImmutable($order['date']);
                } catch (\Exception $e) {
                    throw new RequestValidationException("Invalid 'orderInfo.date' parameter. Should be a valid ISO8601 datetime string.");
                }
            }

            if (isset($order['orderAmount']) && (!is_numeric($order['orderAmount']) || $order['orderAmount'] < 0.01)) {
                throw new RequestValidationException("Invalid 'orderInfo.orderAmount' parameter. Should be a numeric value >= 0.");
            }
            if (isset($order['orderCurrency']) && (!in_array(strtoupper($order['orderCurrency']), MaibCheckoutSdk::CURRENCIES, true))) {
                throw new RequestValidationException("Invalid 'orderInfo.orderCurrency' parameter. Should be one of 'MDL', 'EUR', or 'USD'.");
            }

            if (isset($order['deliveryAmount']) && (!is_numeric($order['deliveryAmount']) || $order['deliveryAmount'] < 0.01)) {
                throw new RequestValidationException("Invalid 'orderInfo.deliveryAmount' parameter. Should be a numeric value >= 0.");
            }
            if (isset($order['deliveryCurrency']) && (!in_array(strtoupper($order['deliveryCurrency']), MaibCheckoutSdk::CURRENCIES, true))) {
                throw new RequestValidationException("Invalid 'orderInfo.deliveryCurrency' parameter. Should be one of 'MDL', 'EUR', or 'USD'.");
            }

            if (isset($order['items'])) {
                if (!is_array($order['items'])) {
                    throw new RequestValidationException("Invalid 'orderInfo.items' parameter. Should be an array.");
                }

                foreach ($order['items'] as $i => $item) {
                    if (!is_array($item)) {
                        throw new RequestValidationException("Invalid 'orderInfo.items[$i]' parameter. Each item should be an object.");
                    }

                    if (isset($item['externalId']) && (!is_string($item['externalId']) || trim($item['externalId']) === '')) {
                        throw new RequestValidationException("Invalid 'orderInfo.items[$i].externalId' parameter. Should be a non-empty string.");
                    }

                    if (isset($item['title']) && (!is_string($item['title']) || trim($item['title']) === '')) {
                        throw new RequestValidationException("Invalid 'orderInfo.items[$i].title' parameter. Should be a non-empty string.");
                    }

                    if (isset($item['amount']) && (!is_numeric($item['amount']) || $item['amount'] < 0.01)) {
                        throw new RequestValidationException("Invalid 'orderInfo.items[$i].amount' parameter. Should be a numeric value >= 0.");
                    }

                    if (isset($item['currency']) && (!in_array(strtoupper($item['currency']), MaibCheckoutSdk::CURRENCIES, true))) {
                        throw new RequestValidationException("Invalid 'orderInfo.items[$i].currency' parameter. Should be one of 'MDL', 'EUR', or 'USD'.");
                    }

                    if (isset($item['quantity']) && (!is_numeric($item['quantity']) || $item['quantity'] < 0.01)) {
                        throw new RequestValidationException("Invalid 'orderInfo.items[$i].quantity' parameter. Should be a numeric value >= 0.");
                    }

                    if (isset($item['displayOrder']) && (!is_int($item['displayOrder']) && !(is_string($item['displayOrder'])))) {
                        throw new RequestValidationException("Invalid 'orderInfo.items[$i].displayOrder' parameter. Should be an integer >= 0.");
                    }
                }
            }
        }
    }

    private static function validateRefundParams($data, $requiredParams)
    {
        foreach ($requiredParams as $param) {
            if (!isset($data[$param])) {
                throw new RequestValidationException("Missing required parameter: '$param'");
            }
        }

        self::validateIdParam($data['payId']);

        if (isset($data['amount'])) {
            if (!is_numeric($data['amount']) || $data['amount'] < 0.01) {
                throw new RequestValidationException("Invalid 'amount' parameter. Should be a numeric value > 0.");
            }
        }

        if (isset($data['reason'])) {
            if (!is_string($data['reason'])) {
                throw new RequestValidationException("Invalid 'reason' parameter. Should be a string.");
            }
        }

        self::validateUrlIfPresent($data, 'callbackUrl');
    }

    private static function validateCheckoutFilter($data)
    {
        if (isset($data['id'])) {
            self::validateIdParam($data['id']);
        }

        if (isset($data['orderId'])) {
            if (!is_string($data['orderId'])) {
                throw new RequestValidationException("Invalid 'orderId' parameter. Should be a string.");
            }
        }

        if (isset($data['status'])) {
            if (!in_array($data['status'], MaibCheckoutSdk::CHECKOUT_STATUSES, true)) {
                throw new RequestValidationException("Invalid 'status' parameter. Allowed values: WaitingForInit, Initialized, PaymentMethodSelected, Completed, Expired, Abandoned, Cancelled, Failed.");
            }
        }

        if (isset($data['minAmount'])) {
            if (!is_numeric($data['minAmount']) || $data['minAmount'] < 0.01) {
                throw new RequestValidationException("Invalid 'minAmount' parameter. Should be a numeric value >= 0.");
            }
        }

        if (isset($data['maxAmount'])) {
            if (!is_numeric($data['maxAmount']) || $data['maxAmount'] < 0.01) {
                throw new RequestValidationException("Invalid 'maxAmount' parameter. Should be a numeric value >= 0.");
            }
        }

        if (isset($data['minAmount']) && isset($data['maxAmount'])) {
            if ((float)$data['minAmount'] > (float)$data['maxAmount']) {
                throw new RequestValidationException("'minAmount' cannot be greater than 'maxAmount'.");
            }
        }

        if (isset($data['currency'])) {
            if (!in_array(strtoupper($data['currency']), MaibCheckoutSdk::CURRENCIES, true)) {
                throw new RequestValidationException("Invalid 'currency' parameter. Currency should be one of 'MDL', 'EUR', or 'USD'.");
            }
        }

        if (isset($data['language'])) {
            if (!in_array(strtoupper($data['language']), MaibCheckoutSdk::LANGUAGES, true)) {
                throw new RequestValidationException("Invalid 'language' parameter. Allowed values: 'EN', 'RU', 'RO'.");
            }
        }

        if (isset($data['payerName'])) {
            if (!is_string($data['payerName']) || trim($data['payerName']) === '') {
                throw new RequestValidationException("Invalid 'payerName' parameter. Should be a non-empty string.");
            }
        }

        if (isset($data['payerEmail'])) {
            if (!is_string($data['payerEmail']) || !filter_var($data['payerEmail'], FILTER_VALIDATE_EMAIL)) {
                throw new RequestValidationException("Invalid 'payerEmail' parameter. Please provide a valid email address.");
            }
        }

        if (isset($data['payerPhone'])) {
            if (!is_string($data['payerPhone']) || trim($data['payerPhone']) === '') {
                throw new RequestValidationException("Invalid 'payerPhone' parameter. Should be a non-empty string.");
            }
        }

        if (isset($data['payerIp'])) {
            if (!is_string($data['payerIp']) || !filter_var($data['payerIp'], FILTER_VALIDATE_IP)) {
                throw new RequestValidationException("Invalid 'payerIp' parameter. Please provide a valid IP address.");
            }
        }

        self::validateDateRange($data, 'cancelledAtFrom', 'cancelledAtTo');
        self::validateDateRange($data, 'createdAtFrom', 'createdAtTo');
        self::validateDateRange($data, 'expiresAtFrom', 'expiresAtTo');
        self::validateDateRange($data, 'failedAtFrom', 'failedAtTo');
        self::validateDateRange($data, 'completedAtFrom', 'completedAtTo');

        if (isset($data['count'])) {
            if ((!is_int($data['count']) && !(is_string($data['count'])))
                || (int)$data['count'] <= 0) {
                throw new RequestValidationException("Invalid 'count' parameter. Should be an integer > 0.");
            }
        }

        if (isset($data['offset'])) {
            if ((!is_int($data['offset']) && !(is_string($data['offset'])))
                || (int)$data['offset'] < 0) {
                throw new RequestValidationException("Invalid 'offset' parameter. Should be an integer >= 0.");
            }
        }

        if (isset($data['sortBy'])) {
            if (!in_array($data['sortBy'], MaibCheckoutSdk::SORT_FIELDS, true)) {
                throw new RequestValidationException("Invalid 'sortBy' parameter. Allowed values: CreatedAt, Amount, Status, ExpiresAt, FailedAt, CancelledAt, CompletedAt.");
            }
        }

        if (isset($data['order'])) {
            if (!in_array(strtoupper($data['order']), MaibCheckoutSdk::ORDER_OPTIONS, true)) {
                throw new RequestValidationException("Invalid 'order' parameter. Allowed values: Asc, Desc.");
            }
        }
    }
    
    private static function validateUrlIfPresent($data, $key)
    {
        if (!isset($data[$key])) return;

        if (!is_string($data[$key]) || !filter_var($data[$key], FILTER_VALIDATE_URL)) {
            throw new RequestValidationException("Invalid '{$key}' parameter. Should be a valid URL string.");
        }
    }

    private static function validateDateRange(array $data, $fromKey, $toKey)
    {
        // validate from
        if (isset($data[$fromKey])) {
            if (!is_string($data[$fromKey]) || trim($data[$fromKey]) === '') {
                throw new RequestValidationException("Invalid '{$fromKey}' parameter. Should be an ISO8601 datetime string.");
            }
            try {
                new \DateTimeImmutable($data[$fromKey]);
            } catch (\Exception $e) {
                throw new RequestValidationException("Invalid '{$fromKey}' parameter. Should be a valid ISO8601 datetime string.");
            }
        }

        // validate to
        if (isset($data[$toKey])) {
            if (!is_string($data[$toKey]) || trim($data[$toKey]) === '') {
                throw new RequestValidationException("Invalid '{$toKey}' parameter. Should be an ISO8601 datetime string.");
            }
            try {
                new \DateTimeImmutable($data[$toKey]);
            } catch (\Exception $e) {
                throw new RequestValidationException("Invalid '{$toKey}' parameter. Should be a valid ISO8601 datetime string.");
            }
        }

        // compare from <= to
        if (isset($data[$fromKey]) && isset($data[$toKey])) {
            if (new \DateTimeImmutable($data[$fromKey]) > new \DateTimeImmutable($data[$toKey])) {
                throw new RequestValidationException("'{$fromKey}' cannot be greater than '{$toKey}'.");
            }
        }
    }

}
