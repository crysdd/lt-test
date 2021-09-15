<?php

namespace App\Http\Services\V1;

class ExerciseService
{

    const EXERCISE = [
        'Fundamentals' => 1,
        'String' => 2,
        'Algorithms' => 3,
        'Mathematic' => 4,
        'Performance' => 5,
        'Booleans' => 6,
        'Functions' => 7,
    ];

    public static function getExerciseId($name)
    {
        return self::EXERCISE[$name] ?? null;
    }
}
