<?php

namespace App\Models;

class User extends \Illuminate\Database\Eloquent\Model
{
    /**
     * Getter to check if user can list streams. Checks for "is_admin" and "view_stream_list".
     *
     * @return bool
     */
    public function getCanListStreamsAttribute(): bool
    {
        return $this->is_admin || $this->view_stream_list;
    }
}