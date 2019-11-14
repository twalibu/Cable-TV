<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    /**
     * Get the Client Region.
     */
    public function region()
    {
        return $this->belongsTo('App\Region');
    }

    /**
     * Get the Tenant.
     */
    public function tenant()
    {
        return $this->belongsTo('App\Tenant');
    }

    /**
     * Get Client Cables
     */
    public function cables()
    {
        return $this->hasMany('App\Cable');
    }
}
