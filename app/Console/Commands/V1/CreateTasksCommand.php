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
        $user_ids = User::select('id')->orderBy('id')->get()->pluck('id');
        $all_exercises_ids = $all_exercises_ids_copy = Exercise::inRandomOrder()->get()->pluck('id')->toArray();
        $limit = 10;
        $offset = 0;
        foreach ($user_ids as $id) {
            if ((count($all_exercises_ids) - $offset) < $limit) {
                shuffle($all_exercises_ids_copy);
                $all_exercises_ids = $all_exercises_ids_copy;
                $offset = 0;
            }
            $exercises = array_slice($all_exercises_ids, $offset, $limit, true);
            $offset += $limit;
            $tasks = [];
            foreach ($exercises as $exercise) {
                $tasks[] = [
                    'user_id' => $id,
                    'exercise_id' => $exercise,
                    'day' => now()->format('Y-m-d'),
                ];
            }
            try {
                Task::insert($tasks); // here for prevent crash on big tables
            } catch (\Throwable $th) {
                Log::debug('v1_tasks:create' . $th->getMessage());
            }
        }

        return 0;
    }
}
