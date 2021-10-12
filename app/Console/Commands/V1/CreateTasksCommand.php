<?php

namespace App\Console\Commands\V1;

use App\Models\Exercise;
use App\Models\Task;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CreateTasksCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'v1_tasks:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create tasks for users on this day';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $user_ids = User::allIds();
        $all_exercises_ids = $all_exercises_ids_copy = Exercise::allIdsInRandomOrder();
        $limit = 10;
        $offset = 0;
        foreach ($user_ids as $id) {
            // if we need more exercise than we have, give it from copy
            if ((count($all_exercises_ids) - $offset) < $limit) {
                $all_exercises_ids = $this->shuffleExercise($all_exercises_ids_copy);
                $offset = 0;
            }
            // give only $limit exercises for user
            $exercise_for_user = array_slice($all_exercises_ids, $offset, $limit, true);
            $offset += $limit;
            $tasks = $this->makeUserListExercises($id, $exercise_for_user);
            try {
                Task::insert($tasks); // here for prevent crash on big tables
            } catch (\Throwable $th) {
                Log::debug('v1_tasks:create' . $th->getMessage());
            }
        }
        return 0;
    }

    private function shuffleExercise(&$all_exercises_ids_copy)
    {
        shuffle($all_exercises_ids_copy);
        return $all_exercises_ids_copy;
    }

    private function makeUserListExercises($user_id, $exercise_for_user)
    {
        $tasks = [];
        foreach ($exercise_for_user as $exercise) {
            $tasks[] = [
                'user_id' => $user_id,
                'exercise_id' => $exercise,
                'day' => now()->format('Y-m-d'),
            ];
        }
        return $tasks;
    }
}
