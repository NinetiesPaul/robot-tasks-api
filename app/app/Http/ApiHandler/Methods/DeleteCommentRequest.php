<?php

namespace App\Http\ApiHandler\Methods;

use App\Http\ApiHandler\TaskApi;
use Exception;

class DeleteCommentRequest extends TaskApi
{
    protected $methodUrl = '/api/task/comment';

    protected $commentId = 0;

    public function __construct($commentId)
    {
        $this->commentId = $commentId;

        parent::__construct();
        $this->execute();
    }

    public function execute()
    {
        $response = $this->handleRequest('delete', $this->methodUrl, [], $this->commentId);
        self::logRequest("Request successful: Comment id $this->commentId was deleted.");
    }
}
