<?php

namespace App\Http\ApiHandler\Methods;

use App\Http\ApiHandler\TaskApi;
use Exception;

class ViewTaskRequest extends TaskApi
{
    protected $methodUrl = '/api/task/view';

    protected $taskId = 0;

    public function __construct($id)
    {
        parent::__construct();

        $this->taskId = $id;
    }

    public function execute()
    {
        try{
            $response = $this->handleRequest('get', $this->methodUrl, [], $this->taskId);
            self::logRequest("Request successful: Task id " . $response['data']['id'] . " retrieved");

            $taskComments = array_column($response['data']['comments'], 'id');
            if (count($taskComments) > 0) {
                return $taskComments[array_rand($taskComments)];
            }
            
            self::logRequest("Request finished with no changes: Task have 0 comments");
            return null;
        } catch (Exception $ex) {
            $exception = ($ex->getMessage()) ?? 'Check api log';
            self::logRequest("Request failed: " . $exception);

            if ($ex->getCode() == 401) {
                self::retrieveToken(true);
            }
        }
    }
}
