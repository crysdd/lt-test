<?php

namespace App\Http\Services\V1;

use App\Models\Task;
use Illuminate\Support\Facades\DB;

class TaskService
{
    public function getDailyTasksForUser(int $id)
    {
        $tasks = DB::table('exercises as e')
            ->join('tasks as t', 't.exercise_id', '=', 'e.id')
            ->select('t.id', 't.user_id', 't.day', 't.done', 'e.category_id', 'e.name', 'e.text')
            ->where('t.user_id', $id)
            ->where('t.day', now()->format('Y-m-d'))
            ->paginate();
        $tasks->getCollection()->transform(function ($e) {
            $e->category_name = ExerciseService::getExerciseName($e->category_id);
            return $e;
        });

        return $tasks;
    }

    public function createNewTodayTask($user_id, $exercise_id)
    {
        return Task::create([
            'user_id' => $user_id,
            'exercise_id' => $exercise_id,
            'day' => now()->format('Y-m-d'),
        ]);
    }
}
