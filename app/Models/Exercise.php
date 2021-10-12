<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exercise extends Model
{
    use HasFactory;

    public function scopeAllIdsInRandomOrder($query)
    {
        return $query->inRandomOrder()->get()->pluck('id')->toArray();
    }

    public function scopeNotCurrentDayExercise($query, array $day_tasks)
    {
        return $query->whereNotIn('id', $day_tasks)->inRandomOrder()->first();
    }
}
