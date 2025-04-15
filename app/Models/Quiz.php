<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Quiz extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'time_limit',
        'total_marks',
        'is_published',
        'start_time',
        'end_time',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function quizAttempts()
    {
        return $this->hasMany(QuizAttempt::class);
    }
}
