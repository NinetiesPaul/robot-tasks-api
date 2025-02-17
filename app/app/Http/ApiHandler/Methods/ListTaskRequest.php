<?php

namespace App\Http\ApiHandler\Methods;

use App\Http\ApiHandler\TaskApi;
use Exception;

class ListTaskRequest extends TaskApi
{
    protected $methodUrl = '/api/task/list';

    public function __construct()
    {
        parent::__construct();
    }

    public function execute($params = [])
    {
        $response = $this->handleRequest('get', $this->methodUrl, [], '', $params);
        self::logRequest("Request successful: Retrieved " . $response['data']['total'] . " tasks");

        if (isset($params['assigned'])) {
            if ($response['data']['total'] > 0) {
                $taskAssignees = array_column($response['data']['tasks'][array_rand(array_keys($response['data']['tasks']))]['assignees'], 'id');
                return $taskAssignees[array_rand($taskAssignees)];
            }
        }

        $taskIds = array_column($response['data']['tasks'], 'id');
        if (count($taskIds) > 0) {
            return $taskIds[array_rand($taskIds)];
        }
        
        self::logRequest("Request finished with no changes: No tasks could be found with parameters: '" . self::prepareParams($params) . "'");
        return null;
    }
}
