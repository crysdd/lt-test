<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\TaskRequest;
use App\Http\Services\V1\ExerciseService;
use App\Http\Services\V1\TaskService;
use App\Models\Exercise;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Display a listing of the tasks.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, TaskService $task_service)
    {
        $user = $request->user();
        $tasks = $task_service->getDailyTasksForUser($user->id);

        return $tasks;
    }

    /**
     * Set task as done
     *
     * @return \Illuminate\Http\Response
     */
    public function done(TaskRequest $request)
    {
        $task = Task::currentTask($request);
        if (! $task instanceof Task) {
            return response(['errors' => 'task not found'], 404);
        }
        $task->update(['done' => true]);

        return response(['data' => ['success' => true]]);
    }

    /**
     * Re Release task.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function reReleaseTask(TaskRequest $request, TaskService $task_service)
    {
        $task = Task::currentTask($request);
        if (! $task instanceof Task) {
            return response(['errors' => 'task not found'], 404);
        }
        $day_tasks = $request->user()->dayTasks()->get()->pluck('exercise_id')->toArray();
        $exercise = Exercise::notCurrentDayExercise($day_tasks);
        if (! $exercise instanceof Exercise) {
            return response(['errors' => ['No one new exercise exists']], 404);
        }
        $task->delete();
        $new_task = $task_service->createNewTodayTask($request->user()->id, $exercise->id);
        $new_task->load('exercise');

        return response(['data' => [
            'id' => $new_task->id,
            'user_id' => $request->user()->id,
            'day' => $new_task->day,
            'done' => (int) $new_task->done,
            'category_id' => $new_task->exercise->category_id,
            'name' => $new_task->exercise->name,
            'text' => $new_task->exercise->text,
            'category_name' => ExerciseService::getExerciseName($new_task->exercise->category_id),
        ]]);
    }
}
