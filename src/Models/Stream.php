<?php

namespace App\Models;

class Stream extends \Illuminate\Database\Eloquent\Model
{
    public function user()
    {
        return $this->belongsTo(User::class, 'id');
    }
}