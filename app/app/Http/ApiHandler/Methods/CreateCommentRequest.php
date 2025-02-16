<?php

namespace App\Http\ApiHandler\Methods;

use App\Http\ApiHandler\TaskApi;
use Exception;

class CreateCommentRequest extends TaskApi
{
    protected $methodUrl = '/api/task/comment';

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
        try {
            $response = $this->handleRequest('post', $this->methodUrl, $this->payload, $this->taskId);
            self::logRequest("Request successful: Comment id " . $response['data']['id'] . " created");
        } catch (Exception $ex) {
            $exception = ($ex->getMessage()) ?? 'Check api log';
            self::logRequest("Request failed: " . $exception);

            if ($ex->getCode() == 401) {
                self::retrieveToken(true);
                self::logRequest("Retrying request");
                new self($this->payload, $this->taskId);
            }
        }
    }
}
