<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'exercise_id',
        'day',
        'done',
    ];

    public function exercise()
    {
        return $this->belongsTo(Exercise::class);
    }

    public function scopeCurrentTask($query, $request)
    {
        return $query->where('id', $request->id)->where('user_id', $request->user()->id)->first();
    }
}
