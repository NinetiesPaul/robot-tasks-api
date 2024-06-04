<?php

namespace App\Http\ApiHandler\Methods;

use App\Http\ApiHandler\TaskApi;
use Exception;

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
        try {
            $response = $this->handleRequest('post', $this->methodUrl, $this->payload);
            self::logRequest("Request successful: Task id " . $response['data']['id'] . " created");
        } catch (Exception $ex) {
            $exception = ($ex->getMessage()) ?? 'Check api log';
            self::logRequest("Request failed: " . $exception);

            if ($ex->getCode() == 401) {
                self::retrieveToken(true);
                self::logRequest("Retrying request");
                new self($this->payload);
            }
        }
    }
}
