<?php

namespace App;

use App\Exceptions\MiddlewareException;
use App\SAPAccessToken;
use Ixudra\Curl\Facades\Curl;

class SAPOAuth2Client
{
    private $clientSecret;
    private $clientId;
    private $tokenEndpoint;
    private $accessToken;
    
    public function __construct()
    {
        $this->clientId = env('SAP_CLIENT_ID');
        $this->clientSecret = env('SAP_CLIENT_SECRET');
        $this->tokenEndpoint = env('SAP_TOKEN_ENDPOINT');
        
        $this->setAccessToken();
    }
    
    /**
     * Set the access token from the SAP OAuth2 Server.
     *
     * @return void
     */
    private function setAccessToken()
    {
        $accessToken = SAPAccessToken::first();
        
        if (!$accessToken || $accessToken->hasExpired()) {
            
            // Get access token from OAuth2 Server, requires basic auth header.
            $response = Curl::to($this->tokenEndpoint)
                ->withHeader('Authorization: Basic ' . base64_encode($this->clientId . ':' . $this->clientSecret))
                ->withTimeout(10)
                ->asJson()
                ->withResponseHeaders()
                ->returnResponseObject()
                ->enableDebug(storage_path('logs/curl-logs-' . date('Y-m-d') . '.txt'))
                ->post();
                
            if (($response->status == 200) && !empty($response->content->access_token)) {
                
                // Purge old access tokens.
                $this->purgeAccessTokens();
                
                $token = new SAPAccessToken;
                $token->token = $response->content->access_token;
                $token->scope = $response->content->scope;
                $token->expires_at = $response->content->expires_in;
                $token->save();
                
                $this->accessToken = $token->getToken();
                
            } else {
                $message = 'Error fetching token from SAP middleware.' . "\n";
                $message .= json_encode($response);
                throw new MiddlewareException($message);
            }
        } else {
            $this->accessToken = $accessToken->getToken();
        }
    }
    
    /**
     * Reset the access token if there is an unauthenticated response.
     *
     * @return void
     */
    public function resetAccessToken()
    {
        $this->purgeAccessTokens();
        $this->setAccessToken();
    }
    
    /**
     * Get the access token.
     *
     * @return string
     */
    protected function getAccessToken()
    {
        return $this->accessToken;
    }
    
    /**
     * Purge all access tokens.
     *
     * @return void
     */
    protected function purgeAccessTokens()
    {
        $oldTokens = SAPAccessToken::all();
        
        if ($oldTokens && count($oldTokens)) {
            foreach ($oldTokens as $oldToken) {
                $oldToken->delete();
            }
        }
    }
    
    /**
     * Perform a POST request to the api.
     *
     * @params string $endpoint
     * @params array $data
     * @params array $headers
     * @return \Ixudra\Curl\Facades\Curl $response
     */
    public function apiPostRequest($endpoint, $data = [], $headers = [])
    {
        $response = Curl::to($endpoint)
            ->withData($data)
            ->withHeaders($headers)
            ->withHeader('Authorization: Bearer ' . $this->getAccessToken())
            ->withTimeout(10)
            ->asJson()
            ->withResponseHeaders()
            ->returnResponseObject()
            ->enableDebug(storage_path('logs/curl-logs-' . date('Y-m-d') . '.txt'))
            ->post();
            
        return $response;
    }
    
    /**
     * Perform a GET request to the api.
     *
     * @params string $endpoint
     * @params array $data
     * @params array $headers
     * @return \Ixudra\Curl\Facades\Curl $response
     */
    public function apiGetRequest($endpoint, $data = [], $headers = [])
    {
        $response = Curl::to($endpoint)
            ->withData($data)
            ->withHeaders($headers)
            ->withHeader('Authorization: Bearer ' . $this->getAccessToken())
            ->withTimeout(10)
            ->asJson()
            ->withResponseHeaders()
            ->returnResponseObject()
            ->enableDebug(storage_path('logs/curl-logs-' . date('Y-m-d') . '.txt'))
            ->get();
            
        return $response;
    }
    
    /**
     * Perform a PUT request to the api.
     *
     * @params string $endpoint
     * @params array $data
     * @params array $headers
     * @return \Ixudra\Curl\Facades\Curl $response
     */
    public function apiPutRequest($endpoint, $data = [], $headers = [])
    {
        $response = Curl::to($endpoint)
            ->withData($data)
            ->withHeaders($headers)
            ->withHeader('Authorization: Bearer ' . $this->getAccessToken())
            ->withTimeout(10)
            ->asJson()
            ->withResponseHeaders()
            ->returnResponseObject()
            ->enableDebug(storage_path('logs/curl-logs-' . date('Y-m-d') . '.txt'))
            ->put();
            
        return $response;
    }
    
    /**
     * Perform a PATCH request to the api.
     *
     * @params string $endpoint
     * @params array $data
     * @params array $headers
     * @return \Ixudra\Curl\Facades\Curl $response
     */
    public function apiPatchRequest($endpoint, $data = [], $headers = [])
    {
        $response = Curl::to($endpoint)
            ->withData($data)
            ->withHeaders($headers)
            ->withHeader('Authorization: Bearer ' . $this->getAccessToken())
            ->withTimeout(10)
            ->asJson()
            ->withResponseHeaders()
            ->returnResponseObject()
            ->enableDebug(storage_path('logs/curl-logs-' . date('Y-m-d') . '.txt'))
            ->patch();
            
        return $response;
    }
    
    /**
     * Perform a DELETE request to the api.
     *
     * @params string $endpoint
     * @params array $data
     * @params array $headers
     * @return \Ixudra\Curl\Facades\Curl $response
     */
    public function apiDeleteRequest($endpoint, $data = [], $headers = [])
    {
        $response = Curl::to($endpoint)
            ->withData($data)
            ->withHeaders($headers)
            ->withHeader('Authorization: Bearer ' . $this->getAccessToken())
            ->withTimeout(10)
            ->asJson()
            ->withResponseHeaders()
            ->returnResponseObject()
            ->enableDebug(storage_path('logs/curl-logs-' . date('Y-m-d') . '.txt'))
            ->delete();
            
        return $response;
    }
}
