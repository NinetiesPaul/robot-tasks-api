<?php

namespace App\Http\ApiHandler;

use App\Http\ApiHandler\Methods\LoginRequest;
use App\Models\RequestLogs;
use DateTime;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TaskApi
{
    protected $url = '';

    protected $token = '';

    protected $databaseLog = [];

    public function __construct($skipToken = false)
    {
        $this->url = $this->retrieveUrl();
        if (!$skipToken) {
            $this->token = self::retrieveToken();
        }
    }

    protected function handleRequest(string $methodType, string $methodUrl, array $payload = [], string $targetId = '', array $params = [])
    {
        self::logRequest(strtoupper($methodType) . " $methodUrl" . (($targetId) ? "/$targetId" : '') . (($params) ? "?" . self::prepareParams($params) : ''));
        if ($payload) {
            //self::logRequest("With payload: " . json_encode($payload));
        }

        $response = Http::withToken($this->token);

        if ($params) {
            $response->withQueryParameters($params);
        }
        
        $requestedAt = new DateTime();
        $this->databaseLog = [
            'type' => $methodType,
            'host' => $this->url,
            'url' => "$methodUrl" . (($targetId) ? "/$targetId" : ''),
            'params' => (($params) ? "?" . $this->prepareParams($params) : null),
            'request_body' => (($payload) ? json_encode($payload) : json_encode("")),
            'requested_at' => $requestedAt->format('Y-m-d h:i:s.u'),
        ];

        $response = $response->{$methodType}($this->url . $methodUrl . (($targetId) ? "/$targetId" : ''), $payload);
        $responseStatus = $response->status();
        $responseJson = $response->json();

        $respondedAt = new DateTime();
        $duration = $requestedAt->diff($respondedAt);
        $this->databaseLog['response_body'] = json_encode($responseJson);
        $this->databaseLog['responded_at'] = $respondedAt->format('Y-m-d h:i:s.u');
        $this->databaseLog['duration'] = $duration->format('%I:%S:%F');
        $this->databaseLog['status'] = $responseStatus;
        
        self::logRequest("API Response Code: " . $responseStatus);
        //self::logRequest("API Response Body: " . json_encode($responseJson));

        if (!in_array($responseStatus, [ 200, 201, 202, 204 ])) {
            RequestLogs::create($this->databaseLog);
            $message = json_encode($response['message']);
            throw new Exception($message, $responseStatus);
        }

        RequestLogs::create($this->databaseLog);
        return $response;
    }

    protected static function retrieveToken($forceRefresh = false)
    {
        $token = Cache::get('token', false);
        if (!$token || $forceRefresh){
            $logMessage = (!$token) ? "Token not found on cache. Requesting new" : "Requesting new token forcibly";
            self::logRequest($logMessage);
            new LoginRequest();
            $token = Cache::get('token');
        }

        self::logRequest("Token retrieved");
        return $token;
    }

    protected static function logRequest($message)
    {
        Log::channel('requests')->info("[LOG] " . $message);
    }

    protected static function prepareParams($params)
    {
        $stringParam = "";
        $nAttrs = count($params);
        foreach ($params as $attr => $value) {
            $stringParam .= "$attr=$value";
            if (count($params) > 1 && $nAttrs > 1) {
                $nAttrs -= 1;
                $stringParam .= "&";
            }
        }

        return $stringParam;
    }

    protected function retrieveUrl()
    {
        return Config::get('app.api_url');
        /*$hosts = explode(";", Config::get('app.api_url'));
        $this->url = $hosts[array_rand($hosts)];

        $users = explode(";", Config::get('app.api_credentials'));
        $user = $users[array_rand($users)];
        $user = explode(":", $user);*/
        
    }
}
