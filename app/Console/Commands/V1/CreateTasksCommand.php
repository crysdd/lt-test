<?php

namespace App\Console\Commands\V1;

use App\Models\Exercise;
use App\Models\Task;
use App\Models\User;
use Illuminate\Console\Command;

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

        foreach ($user_ids as $id) {
            $exercises = Exercise::inRandomOrder()->select('id')->limit(10)->get()->pluck('id')->toArray();
            $tasks = [];
            foreach ($exercises as $exercise) {
                $tasks[] = [
                    'user_id' => $id,
                    'exercise_id' => $exercise,
                    'day' => now()->format('Y-m-d'),
                ];
            }
            Task::insert($tasks);
        }

        return 0;
    }
}
