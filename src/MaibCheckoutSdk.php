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

class ClientException extends RuntimeException {}

class MaibCheckoutSdk
{
    // maib ecommerce API base url
    const BASE_URL = "https://api.maibmerchants.md/v2/";

    // maib ecommerce API endpoints
    const CREATE_CHECKOUT = "checkouts";
    const GET_TOKEN = "auth/token";
    const GET_ALL_CHECKOUTS = "checkouts";
    const GET_CHECKOUT = "checkouts/{Id}";
    const GET_PAYMENT = "payments/{Id}";
    const REFUND = "payments/{Id}/refund";
    const GET_REFUND = "payments/refunds/{Id}";

    // HTTP request methods
    const HTTP_GET = "GET";
    const HTTP_POST = "POST";

    const CURRENCIES = ['MDL', 'EUR', 'USD'];
    const LANGUAGES  = ['EN', 'RU', 'RO'];

    const ORDER_OPTIONS  = ['ASC', 'DESC'];

    const SORT_FIELDS = [
        'CreatedAt',
        'Amount',
        'Status',
        'ExpiresAt',
        'FailedAt',
        'CancelledAt',
        'CompletedAt',
    ];

    const CHECKOUT_STATUSES = [
        'WaitingForInit',
        'Initialized',
        'PaymentMethodSelected',
        'Completed',
        'Expired',
        'Abandoned',
        'Cancelled',
        'Failed'
    ];

    private static $instance;
    private $baseUri;

    public function __construct($baseUri = null)
    {
        if(is_null($baseUri))
        {
            $this->baseUri = MaibCheckoutSdk::BASE_URL;
        }
        else
        {
            $this->baseUri = $baseUri;
        }
    }

    /**
     * Get the instance of MaibCheckoutSdk (Singleton pattern)
     *
     * @return MaibCheckoutSdk
     */
    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Send a POST request
     *
     * @param string $uri
     * @param array $data
     * @param string|null $token
     * @return mixed
     * @throws ClientException
     */
    public function post($uri, array $data = [], $token = null)
    {
        $url = $this->buildUrl($uri);
        return $this->sendRequest(self::HTTP_POST, $url, $data, $token);
    }

    /**
     * Send a GET request
     *
     * @param string $path
     * @param string $token
     * @param array $params
     * @return mixed
     * @throws ClientException
     */
    public function get($path, $token, array $params = [])
    {
        $url = $this->buildUrl($path, $params);
        return $this->sendRequest(self::HTTP_GET, $url, [], $token);
    }

    /**
     * Build the complete URL for the request
     *
     * @param string $path
     * @param array $params
     * @return string
     */
    private function buildUrl($path, array $params = [])
    {
        $base = rtrim($this->baseUri, '/');
        $path = '/' . ltrim($path, '/');

        $url = $base . $path;

        if (!empty($params)) {
            $query = http_build_query($params, '', '&', PHP_QUERY_RFC3986);
            if ($query !== '') {
                $url .= '?' . $query;
            }
        }

        return $url;
    }


    /**
     * Send a request using cURL and handle the response.
     *
     * @param string $method The HTTP method for the request.
     * @param string $url The complete URL for the request.
     * @param array $data The data to be sent with the request.
     * @param string|null $token The authorization token (optional).
     * @return mixed The decoded response from the API.
     * @throws ClientException if an error occurs during the request or if the response has an error status code.
     */
    private function sendRequest($method, $url, array $data = [], $token = null)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);

        if ($method === self::HTTP_POST) {
            $payload = json_encode($data);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            $headers = ["Content-Type: application/json"];
        } else {
            $headers = [];
        }

        if ($token !== null) {
            $headers[] = "Authorization: Bearer " . $token;
        }

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            $errorMessage = "An error occurred: " . curl_error($ch);
            curl_close($ch);
            throw new ClientException($errorMessage);
        }

        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($statusCode >= 500) {
            $errorMessage = $this->getErrorMessage($response, $statusCode);
            throw new ClientException(
                "An error occurred: HTTP " . $statusCode . ": " . $errorMessage
            );
        }

        $decodedResponse = json_decode($response, false);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new ClientException(
                "Failed to decode response: " . json_last_error_msg()
            );
        }

        // Debugging statement to log the request and response
        error_log(
            "Request: $method $url " .
                json_encode($data) .
                " Response: $response"
        );

        return $decodedResponse;
    }

    /**
     * Retrieves the error message from the API response.
     *
     * @param string $response The API response.
     * @param int $statusCode The HTTP status code of the response.
     * @return string The error message extracted from the response, or a default message if not found.
     */
    private function getErrorMessage($response, $statusCode)
    {
        $errorMessage = "";
        if ($response) {
            $responseObj = json_decode($response);
            if (isset($responseObj->errors[0]->errorMessage)) {
                $errorMessage = $responseObj->errors[0]->errorMessage;
            } else {
                $errorMessage = "Unknown error details.";
            }
        }
        return $errorMessage;
    }
}
