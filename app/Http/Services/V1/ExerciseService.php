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

    const EXERCISE_NAME = [
        1 => 'Fundamentals',
        2 => 'String',
        3 => 'Algorithms',
        4 => 'Mathematic',
        5 => 'Performance',
        6 => 'Booleans',
        7 => 'Functions',
    ];

    public static function getExerciseId($name)
    {
        return self::EXERCISE[$name] ?? null;
    }

    public static function getExerciseName($id)
    {
        return self::EXERCISE_NAME[$id] ?? null;
    }
}
