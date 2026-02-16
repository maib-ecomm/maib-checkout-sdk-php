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

class TokenException extends RuntimeException {}

// Factory class responsible for creating new instances of the MaibCheckoutAuth class.
class MaibCheckoutAuthRequest
{
    /**
     * Creates an instance of the MaibCheckoutAuth class.
     *
     * @return MaibCheckoutAuth The created instance.
     */
    public static function create($baseUrl = null)
    {
        // Create a new instance of the MaibCheckoutSdk class and pass it to the MaibCheckoutAuth constructor.
        $httpClient = new MaibCheckoutSdk($baseUrl);
        return new MaibCheckoutAuth($httpClient);
    }
}

class MaibCheckoutAuth
{
    private $httpClient;
    
    /**
     * Constructs a new MaibCheckoutAuth instance.
     *
     * @param MaibCheckoutSdk $httpClient The HTTP client for sending requests.
     */
    public function __construct(MaibCheckoutSdk $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * Generates a new access token using the given clientId and clientSecret or refresh token.
     *
     * @param string|null $clientId The client ID or refresh token to use for generating the token.
     * @param string|null $clientSecret The client secret to use for generating the token.
     * @return array The response body as an associative array.
     * @throws RuntimeException If the API returns an error response.
     */
    public function generateToken($clientId, $clientSecret)
    {
        if ($clientSecret === null || $clientId === null)
        {
            throw new TokenException("Client ID and Client Secret must be provided!");
        }

        $postData = array();

        $postData['clientId'] = $clientId;
        $postData['clientSecret'] = $clientSecret;

        try
        {
            $response = $this
                ->httpClient
                ->post(MaibCheckoutSdk::GET_TOKEN, $postData);
        }
        catch(\Exception $e)
        {
            throw new TokenException("HTTP error while sending POST request to endpoint generate-token: {$e->getMessage() }");
        }

        return $this->handleResponse($response, MaibCheckoutSdk::GET_TOKEN);
    }

    /**
     * Handles errors returned by the API.
     *
     * @param object $response The API response object.
     * @param string $endpoint The endpoint name.
     * @return mixed The result extracted from the response.
     * @throws RuntimeException If the API returns an error response or an invalid response.
     */
    private function handleResponse($response, $endpoint)
    {
        if (isset($response->ok) && $response->ok)
        {
            if (isset($response->result))
            {
                return $response->result;
            }
            else
            {
                throw new TokenException("Invalid response received from server for endpoint $endpoint: missing 'result' field");
            }
        }
        else
        {
            if (isset($response->errors))
            {
                $error = $response->errors[0];
                throw new TokenException("Error sending request to endpoint $endpoint: {$error->errorMessage} ({$error->errorCode})");
            }
            else
            {
                throw new TokenException("Invalid response received from server for endpoint $endpoint: missing 'ok' and 'errors' fields");
            }
        }
    }
}
