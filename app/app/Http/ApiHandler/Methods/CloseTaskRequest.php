<?php

namespace App\Http\ApiHandler\Methods;

use App\Http\ApiHandler\TaskApi;
use Exception;

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
        try {
            $response = $this->handleRequest('put', $this->methodUrl, [], $this->taskId);
            self::logRequest("Request successful: Task id " . $response['data']['id'] . " closed");
        } catch (Exception $ex) {
            $exception = ($ex->getMessage()) ?? 'Check api log';
            self::logRequest("Request failed: " . $exception);

            if ($ex->getCode() == 401) {
                self::retrieveToken(true);
                self::logRequest("Retrying request");
                new self($this->taskId);
            }
        }
    }
}
