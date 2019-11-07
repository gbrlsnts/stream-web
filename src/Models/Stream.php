<?php

namespace App\Models;

class Stream extends \Illuminate\Database\Eloquent\Model
{
    public function user()
    {
        return $this->belongsTo(User::class, 'id');
    }

    public function tokens()
    {
        return $this->hasMany(Token::class);
    }
}