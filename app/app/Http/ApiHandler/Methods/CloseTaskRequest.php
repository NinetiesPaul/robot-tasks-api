<?php

namespace App\Http\ApiHandler\Methods;

use App\Http\ApiHandler\TaskApi;

class CloseTaskRequest extends TaskApi
{
    protected $methodUrl = '/api/task/close';

    protected $taskId = 0;

    public function __construct($id)
    {
        $this->taskId = $id;

        parent::__construct();
        $this->execute();
    }

    public function execute()
    {
        $response = $this->handleRequest('put', $this->methodUrl, [], $this->taskId);
        $this->logRequest("Request successful: Task id " . $response['data']['id'] . " closed");
    }
}
