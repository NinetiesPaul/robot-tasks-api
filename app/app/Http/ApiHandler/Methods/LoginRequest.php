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
            Cache::forget('token');
            Cache::put('token', $response['token']);
        }
    }
}
