<?php

namespace App\Http\ApiHandler\Methods;

use App\Http\ApiHandler\TaskApi;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

class LoginRequest extends TaskApi
{
    protected $methodUrl = "/login";

    public function __construct()
    {
        $this->execute();
    }

    public function execute()
    {
        $response = $this->handleRequest('post', Config::get('app.api_url') . $this->methodUrl, [
            'username' => Config::get('app.api_username'),
            'password' => Config::get('app.api_password'),
        ]);

        if ($response['token']) {
            Cache::put('token', $response['token']);
        }

        /*$url = Config::get('app.api_url');
        $username = Config::get('app.api_username');
        $password = Config::get('app.api_password');

        $this->logRequest("POST $this->methodUrl");
        try {
            $res = Http::post($url . $this->methodUrl, [
                'username' => $username,
                'password' => $password,
            ]);

            $response = $res->json();
            $this->logRequest("Request successful: Token " . $response['token'] . " created");

            Cache::put('token', $response['token']);
        } catch (Exception $ex) {
            $exception = ($ex->getMessage()) ?? 'Check api log';
            $this->logRequest(__CLASS__ .  " failed: " . $exception);
        }*/
    }
}
