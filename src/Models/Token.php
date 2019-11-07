<?php

namespace App\Models;

class Token extends \Illuminate\Database\Eloquent\Model
{
    public function stream()
    {
        return $this->belongsTo(Stream::class, 'id');
    }
}