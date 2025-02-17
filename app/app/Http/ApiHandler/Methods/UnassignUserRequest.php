<?php

namespace App\Http\ApiHandler\Methods;

use App\Http\ApiHandler\TaskApi;
use Exception;

class UnassignUserRequest extends TaskApi
{
    protected $methodUrl = '/api/task/unassign';

    protected $assignmentId = 0;

    public function __construct($assignmentId)
    {
        $this->assignmentId = $assignmentId;

        parent::__construct();
        $this->execute();
    }

    public function execute()
    {
        $response = $this->handleRequest('delete', $this->methodUrl, [], $this->assignmentId);
        self::logRequest("Request successful: Assignment id $this->assignmentId was deleted.");
    }
}
