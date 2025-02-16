<?php

namespace App\Http\ApiHandler\Methods;

use App\Http\ApiHandler\TaskApi;
use Exception;

class ListUserRequest extends TaskApi
{
    protected $methodUrl = '/api/users/list';

    public function __construct()
    {
        parent::__construct(true);
    }

    public function execute($params = [])
    {
        try{
            $response = $this->handleRequest('get', $this->methodUrl, [], '', $params);
            self::logRequest("Request successful: Retrieved " . $response['data']['total'] . " users");
    
            $userIds = array_column($response['data']['users'], 'id');
            if (count($userIds) > 0) {
                return $userIds[array_rand($userIds)];
            }
            
            self::logRequest("Request finished with no changes: No users found ");
            return null;
        } catch (Exception $ex) {
            $exception = ($ex->getMessage()) ?? 'Check api log';
            self::logRequest("Request failed: " . $exception);

            if ($ex->getCode() == 401) {
                self::retrieveToken(true);
            }
        }
    }
}
