<?php

namespace App\Console\Commands;

use App\Http\ApiHandler\Methods\AssignUserRequest;
use App\Http\ApiHandler\Methods\CloseTaskRequest;
use App\Http\ApiHandler\Methods\CreateCommentRequest;
use App\Http\ApiHandler\Methods\CreateTaskRequest;
use App\Http\ApiHandler\Methods\DeleteCommentRequest;
use App\Http\ApiHandler\Methods\ListTaskRequest;
use App\Http\ApiHandler\Methods\ListUserRequest;
use App\Http\ApiHandler\Methods\LoginRequest;
use App\Http\ApiHandler\Methods\UnassignUserRequest;
use App\Http\ApiHandler\Methods\UpdateTaskRequest;
use App\Http\ApiHandler\Methods\ViewTaskRequest;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ApiCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:api-command {action=random}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Execute a request to the Task API';

    /**
     * Auxiliary variables
     * 
     */

    protected $actions = [ 'create_task', 'update_task', 'close_task', 'assign_task', 'unassign_task' ];

    protected $descriptionArray = [
        'There\'s a new issue identified by the user, it relates to a new functionality recently added',
        'Users complaining of issues with that feature. It seems to be happening for a while now',
        'Lately, a small number of users are complaining of bad operation or corrupted operation with this feature' ];

    protected $titleArray = [ 'Transfer Bank to Bank timing out', 'Teacher unable to update subject grade', 'Unable to call Authentication Endpoint' ];

    protected $typeArray = [ 'feature', 'bugfix', 'hotfix' ];

    protected $statusArray = [ 'in_dev', 'blocked', 'in_qa' ];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Log::channel('requests')->info("*****");
        Log::channel('requests')->info("[LOG] Starting");

        $action = $this->argument('action');
        if ($action == 'random') {
            $action = $this->actions[array_rand($this->actions)];
        }
        
        Log::channel('requests')->info("[LOG] Executing '$action' routine");
        switch ($action) {
            case 'create_task':
                new CreateTaskRequest($this->randomizePaylod());
                break;

            case 'update_task':
                $listTaskRequest = new ListTaskRequest();
                $taskId = $listTaskRequest->execute([ 'status' => 'open' ]);

                if ($taskId) {
                    new UpdateTaskRequest($this->randomizePaylod(true), $taskId);
                }
                break;

            case 'close_task':
                $listTaskRequest = new ListTaskRequest();
                $taskId = $listTaskRequest->execute([ 'status' => $this->statusArray[array_rand($this->statusArray)] ]);

                if ($taskId) {
                    new CloseTaskRequest($taskId);
                }
                break;

            case 'assign_task':
                $listUserRequest = new ListUserRequest();
                $userId = $listUserRequest->execute();

                $listTaskRequest = new ListTaskRequest();
                $taskId = $listTaskRequest->execute([ 'status' => $this->statusArray[array_rand($this->statusArray)] ]);

                if ($taskId) {
                    new AssignUserRequest($taskId, $userId);
                }
                break;

            case 'unassign_task':
                $listTaskRequest = new ListTaskRequest();
                $assignmentId = $listTaskRequest->execute([ 'assigned' => 'true' ]);

                if ($assignmentId) {
                    new UnassignUserRequest($assignmentId);
                }
                break;

            case 'comment_task':
                $listTaskRequest = new ListTaskRequest();
                $taskId = $listTaskRequest->execute();

                if ($taskId) {
                    new CreateCommentRequest([ "text" => "This is a Task Comment created by the Robot"], $taskId);
                }
                break;

            case 'uncomment_task':
                $listTaskRequest = new ListTaskRequest();
                $taskId = $listTaskRequest->execute();

                if ($taskId) {
                    $taskComment = new ViewTaskRequest($taskId);
                    $commentId = $taskComment->execute();
                }

                if ($commentId) {
                    new DeleteCommentRequest($commentId);
                }
                break;

            case 'authenticate':
                new LoginRequest();
                break;
        }

        Log::channel('requests')->info("[LOG] Finished");
    }

    private function randomizePaylod($forUpdate = false)
    {
        $payload = [
            'title' => $this->titleArray[array_rand($this->titleArray)],
            'description' => $this->descriptionArray[array_rand($this->descriptionArray)],
            'type' => $this->typeArray[array_rand($this->typeArray)],
        ];

        if ($forUpdate) {
            $payload['status'] = $this->statusArray[array_rand($this->statusArray)];
        }

        return $payload;
    }
}
