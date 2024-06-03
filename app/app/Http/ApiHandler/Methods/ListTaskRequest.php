<?php

namespace App\Http\ApiHandler\Methods;

use App\Http\ApiHandler\TaskApi;

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
        $this->logRequest("Request successful: Retrieved " . $response['data']['total'] . " tasks");

        $taskIds = array_column($response['data']['tasks'], 'id');
        if (count($taskIds) > 0) {
            return $taskIds[array_rand($taskIds)];
        }
        
        $this->logRequest("Request finished with no changes: No tasks found with status '" . $params['status'] . "'");
        return null;
    }
}
