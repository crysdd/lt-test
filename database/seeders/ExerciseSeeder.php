<?php

namespace Database\Seeders;

use App\Http\Services\V1\ExerciseService;
use App\Models\Exercise;
use Illuminate\Database\Seeder;

class ExerciseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $number = [];
        for ($i=1; $i <= 1000; $i++) {
            $category_id = rand(1, count(ExerciseService::EXERCISE));
            if (isset($number[$category_id])) {
                $number[$category_id]++;
            }else{
                $number[$category_id] = 1;
            }
            $category_name = ExerciseService::getExerciseName($category_id);
            Exercise::factory()->create([
                'category' => $category_id,
                'name' => 'Some exercise about ' . $category_name . ' number ' . $number[$category_id],
                'text' => 'This is exercise ' . $number[$category_id]. '. bla-bla-bla...',
            ]);
        }

    }
}
