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
}
