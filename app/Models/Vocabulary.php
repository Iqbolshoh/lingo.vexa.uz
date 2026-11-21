<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vocabulary extends Model
{
    protected $table = 'vocabulary';
    public $timestamps = false;
    protected $guarded = [];

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }
}
