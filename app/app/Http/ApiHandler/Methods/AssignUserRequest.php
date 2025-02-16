<?php

namespace App\Http\ApiHandler\Methods;

use App\Http\ApiHandler\TaskApi;
use Exception;

class AssignUserRequest extends TaskApi
{
    protected $methodUrl = '/api/task/assign';

    protected $taskId = 0;

    protected $userId = 0;

    public function __construct($taskId, $userId)
    {
        $this->taskId = $taskId;
        $this->userId = $userId;

        parent::__construct();
        $this->execute();
    }

    public function execute()
    {
        try {
            $response = $this->handleRequest('post', $this->methodUrl, [ 'assigned_to' => $this->userId ], $this->taskId);
            self::logRequest("Request successful: Task id " . $response['data']['id'] . " assigned to user " . $this->userId);
        } catch (Exception $ex) {
            self::retrieveToken(true);
            self::logRequest("Retrying request");
            //new self($this->taskId);
        }
    }
}
