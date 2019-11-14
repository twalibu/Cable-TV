<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CableAlert extends Model
{
    /**
     * Get Cable.
     */
    public function cable()
    {
        return $this->belongsTo('App\Cable');
    }
}
