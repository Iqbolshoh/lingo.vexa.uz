<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    public $timestamps = false;
    protected $guarded = [];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function vocabulary()
    {
        return $this->hasMany(Vocabulary::class)->orderBy('sequence');
    }

    public function quiz()
    {
        return $this->hasOne(Quiz::class);
    }
}
