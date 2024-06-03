<?php

namespace App\Http\ApiHandler\Methods;

use App\Http\ApiHandler\TaskApi;

class UpdateTaskRequest extends TaskApi
{
    protected $methodUrl = '/api/task/update';

    protected $payload = [];

    protected $taskId = 0;

    public function __construct($payload, $id)
    {
        $this->payload = $payload;
        $this->taskId = $id;

        parent::__construct();
        $this->execute();
    }

    public function execute()
    {
        $response = $this->handleRequest('put', $this->methodUrl, $this->payload, $this->taskId);
        $this->logRequest("Request successful: Task id " . $response['data']['id'] . " updated");
    }
}
