<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    /**
     * Get Tenant Schedule
     */
    public function schedule()
    {
        return $this->hasOne('App\TenantSchedule');
    }

    /**
     * Get Tenant Subscription
     */
    public function subscription()
    {
        return $this->hasOne('App\TenantSubscription');
    }

    /**
     * Get Tenant Address
     */
    public function address()
    {
        return $this->hasOne('App\TenantAddress');
    }

    /**
     * Get Tenant SMS Details
     */
    public function sms()
    {
        return $this->hasOne('App\TenantSMS');
    }

     /**
     * Get Tenant Contact Persons
     */
    public function contacts()
    {
        return $this->hasMany('App\TenantContact');
    }

    /**
     * Get Tenant Clients
     */
    public function clients()
    {
        return $this->hasMany('App\Client');
    }

    /**
     * Get Tenant Cable Types
     */
    public function types()
    {
        return $this->hasMany('App\CableTypes');
    }

    /**
     * Get Tenant Cables
     */
    public function cables()
    {
        return $this->hasMany('App\Cable');
    }

    /**
     * Get Tenant Users
     */
    public function users()
    {
        return $this->hasMany('App\User');
    }

    /**
     * Get Tenant Roles
     */
    public function roles()
    {
        return $this->hasMany('App\Role');
    }
}
