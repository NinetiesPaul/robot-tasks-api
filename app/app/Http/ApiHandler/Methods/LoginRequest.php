<?php

namespace App\Http\ApiHandler\Methods;

use App\Http\ApiHandler\TaskApi;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;

class LoginRequest extends TaskApi
{
    protected $methodUrl = "/login";

    public function __construct()
    {
        parent::__construct(true);
        $this->execute();
    }

    public function execute()
    {
        $tokens = [];

        $hosts = explode(";", Config::get('app.api_url'));

        if (Config::get('app.api_mode') == "SINGLE") {
            $hosts = [ $hosts[0] ];
        }

        $users = explode(";", Config::get('app.api_credentials'));

        foreach ($users as $user) {
            $user = explode(":", $user);

            foreach ($hosts as $host) {

                $this->forceUrl($host);

                $response = $this->handleRequest('post', $this->methodUrl, [
                    'username' => $user[0],
                    'password' => $user[1],
                ]);

                if ($response['token']) {
                    $tokens[$host] = $response['token'];
                } 
            }
        }

        Cache::forget('token');
        Cache::put('token', $tokens);
    }
}
