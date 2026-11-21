<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    public $timestamps = false;
    protected $guarded = [];

    public function lessons()
    {
        return $this->hasMany(Lesson::class)->orderBy('sequence');
    }
}
