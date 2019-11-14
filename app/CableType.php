<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CableType extends Model
{
    /**
     * Get Cables
     */
    public function cables()
    {
        return $this->hasMany('App\Cable', 'type_id');
    }

    /**
     * Get the Tenant.
     */
    public function tenant()
    {
        return $this->belongsTo('App\Tenant');
    }
}
