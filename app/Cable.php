<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cable extends Model
{
    /**
     * Get the Tenant.
     */
    public function tenant()
    {
        return $this->belongsTo('App\Tenant');
    }

    /**
     * Get Cable Type
     */
    public function type()
    {
        return $this->belongsTo('App\CableType', 'type_id');
    }

    /**
     * Get Cable Client
     */
    public function client()
    {
        return $this->belongsTo('App\Client');
    }

    /**
     * Get Cable Alert
     */
    public function alert()
    {
        return $this->hasOne('App\CableAlert');
    }
}
