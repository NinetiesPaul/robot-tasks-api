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
        $this->token = self::retrieveToken();
    }

    protected function handleRequest(string $methodType, string $methodUrl, array $payload = [], string $targetId = '', array $params = [])
    {
        self::logRequest(strtoupper($methodType) . " $methodUrl" . (($targetId) ? "/$targetId" : '') . (($params) ? "?status=" . $params['status'] : ''));

        if ($payload) {
            self::logRequest("With payload: " . json_encode($payload));
        }

        $response = Http::withToken($this->token);

        if ($params) {
            $response->withQueryParameters($params);
        }
        
        $response = $response->{$methodType}($this->url . $methodUrl . (($targetId) ? "/$targetId" : ''), $payload);
        $responseStatus = $response->status();
        $responseJson = $response->json();
        self::logRequest("API Response Code: " . $responseStatus);
        self::logRequest("API Response Body: " . json_encode($responseJson));

        if ($responseStatus !== 200) {
            $message = (isset($response['msg'])) ? $response['msg'] : $response['message'];
            throw new Exception($message, $responseStatus);
        }

        return $response;
    }

    protected static function retrieveToken($forceRefresh = false)
    {
        self::logRequest("Retrieving token.");

        $token = Cache::get('token', false);
        if (!$token || $forceRefresh){
            self::logRequest("Requesting new token.");
            new LoginRequest();
            $token = Cache::get('token');
        }

        self::logRequest("Token retrieved: " . $token);
        return $token;
    }

    protected static function logRequest($message)
    {
        Log::channel('requests')->info("[LOG] " . $message);
    }
}
