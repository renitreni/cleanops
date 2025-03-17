<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class ItexmoService
{
    private string $endpoint = 'https://api.itexmo.com/api/broadcast';
    private string $email;
    private string $password;
    private string $apiCode;
    private Client $client;

    /**
     * Constructor for ItexmoService
     * 
     * @param string $email Client email
     * @param string $password Client password
     * @param string $apiCode API code for authentication
     */
    public function __construct(string $email, string $password, string $apiCode)
    {
        $this->email = $email;
        $this->password = $password;
        $this->apiCode = $apiCode;
        $this->client = new Client();
    }

    /**
     * Send a broadcast message via Itexmo API
     * 
     * @param array $content Message content and other parameters
     * @return array Response from the API
     * @throws GuzzleException
     */
    public function sendBroadcast(array $content): array
    {
        // Make sure API code is included in the content
        $content['ApiCode'] = $this->apiCode;
        
        try {
            $response = $this->client->post($this->endpoint, [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'Authorization' => 'Basic ' . base64_encode("{$this->email}:{$this->password}")
                ],
                'body' => json_encode($content)
            ]);
            
            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            // You can customize error handling based on your needs
            throw $e;
        }
    }
    
    /**
     * Set a custom endpoint URL
     * 
     * @param string $endpoint New endpoint URL
     * @return void
     */
    public function setEndpoint(string $endpoint): void
    {
        $this->endpoint = $endpoint;
    }
}