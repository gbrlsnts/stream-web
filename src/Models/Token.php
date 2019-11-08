<?php

namespace App\Models;

class Token extends \Illuminate\Database\Eloquent\Model
{
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
    
    public function stream()
    {
        return $this->belongsTo(Stream::class);
    }
}