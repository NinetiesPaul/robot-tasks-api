<?php

namespace App\Http\ApiHandler;

use App\Http\ApiHandler\Methods\LoginRequest;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TaskApi
{
    protected $url = '';

    protected $token = '';

    public function __construct()
    {
        $this->url = Config::get('app.api_url');
        $this->token = $this->retrieveToken();
    }

    protected function handleRequest(string $methodType, string $methodUrl, array $payload = [], string $targetId = '', array $params = [])
    {
        $this->logRequest(strtoupper($methodType) . " $methodUrl" . (($targetId) ? "/$targetId" : '') . (($params) ? "?status=" . $params['status'] : ''));

        if ($payload) {
            $this->logRequest("With payload: " . json_encode($payload));
        }

        try {
            $response = Http::withToken($this->token);

            if ($params) {
                $response->withQueryParameters($params);
            }
            
            $response = $response->{$methodType}($this->url . $methodUrl . (($targetId) ? "/$targetId" : ''), $payload);
            $response = $response->json();
            $this->logRequest("API Response: " . json_encode($response));

            if (!$response['success']) {
                throw new Exception($response['msg']);
            }

            return $response;
        } catch (Exception $ex) {
            $exception = ($ex->getMessage()) ?? 'Check api log';
            $this->logRequest("Request failed: " . $exception);
        }
    }

    protected function retrieveToken()
    {
        $this->logRequest("Retrieving token.");

        $token = Cache::get('token', false);
        if (!$token){
            $this->logRequest("Token not found! Requesting new token.");
            new LoginRequest();
            $token = Cache::get('token');
        }

        $this->logRequest("Token retrieved");
        return $token;
    }

    protected function updateToken()
    {
    }

    protected function logRequest($message)
    {
        Log::channel('requests')->info("[LOG] " . $message);
    }
}
