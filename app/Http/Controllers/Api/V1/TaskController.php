<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\TaskRequest;
use App\Http\Services\V1\ExerciseService;
use App\Models\Exercise;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TaskController extends Controller
{
    /**
     * Display a listing of the tasks.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $exercises = DB::table('exercises as e')
                ->join('tasks as t', 't.exercise_id', '=', 'e.id')
                ->select('t.id', 't.user_id', 't.day', 't.done', 'e.category_id', 'e.name', 'e.text')
                ->where('t.user_id', $user->id)
                ->where('t.day', now()->format('Y-m-d'))
                ->paginate();
        $exercises->getCollection()->transform(function ($e) {
            $e->category_name = ExerciseService::getExerciseName($e->category_id);
            return $e;
        });

        return $exercises;
    }

    /**
     * Set task as done
     *
     * @return \Illuminate\Http\Response
     */
    public function done(TaskRequest $request)
    {
        $task = Task::where('id', $request->id)->where('user_id', $request->user()->id)->first();
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
    public function reReleaseTask(TaskRequest $request)
    {
        $task = Task::where('id', $request->id)->where('user_id', $request->user()->id)->first();
        if (! $task instanceof Task) {
            return response(['errors' => 'task not found'], 404);
        }
        $dayTasks = $request->user()->dayTasks()->get()->pluck('exercise_id')->toArray();
        $exercise = Exercise::whereNotIn('id', $dayTasks)->inRandomOrder()->first();
        if (! $exercise instanceof Exercise) {
            return response(['errors' => ['No one new exercise exists']], 404);
        }
        $task->delete();
        $new_task = Task::create([
            'user_id' => $request->user()->id,
            'exercise_id' => $exercise->id,
            'day' => now()->format('Y-m-d'),
        ]);

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
