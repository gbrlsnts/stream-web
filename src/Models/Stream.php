<?php

namespace App\Models;

class Stream extends \Illuminate\Database\Eloquent\Model
{
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo(User::class, 'id');
    }

    public function tokens()
    {
        return $this->hasMany(Token::class);
    }
}