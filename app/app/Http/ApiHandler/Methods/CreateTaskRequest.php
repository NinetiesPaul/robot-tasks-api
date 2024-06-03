<?php

namespace App\Http\ApiHandler\Methods;

use App\Http\ApiHandler\TaskApi;

class CreateTaskRequest extends TaskApi
{
    protected $methodUrl = '/api/task/create';

    protected $payload = [];

    public function __construct($payload)
    {
        $this->payload = $payload;

        parent::__construct();
        $this->execute();
    }

    public function execute()
    {
        $response = $this->handleRequest('post', $this->methodUrl, $this->payload);
        $this->logRequest("Request successful: Task id " . $response['data']['id'] . " created");
    }
}
