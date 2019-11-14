<?php

namespace App\Http\Controllers;

use Crypt;
use Input;
use Sentinel;

use App\Cable;
use App\Client;
use App\Tenant;
use App\TenantSMS;
use Illuminate\Http\Request;
use infobip\api\client\GetAccountBalance;
use infobip\api\client\SendSingleTextualSms;
use infobip\api\configuration\BasicAuthConfiguration;

class DashboardController extends Controller
{
    public function __construct()
    {
        // Tenant Middleware
        $this->middleware('sentinel.auth', ['only' => ['tenant']]);

        // Admin Middleware
        $this->middleware('sentry.auth', ['only' => ['admin']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function admin()
    {
        $username =  env('INFOBIP_USERNAME');
        $password =  env('INFOBIP_PASSWORD');

        // Initializing GetAccountBalance client with appropriate configuration
        $client = new GetAccountBalance(new BasicAuthConfiguration($username, $password));
        // Executing request
        $response = $client->execute();

        $amount = $response->getBalance();
        $currency = $response->getCurrency();
        $tenants = Tenant::all()->count();
        $clients = Client::all()->count();
        $cables = Cable::all()->count();

        return view('dashboards.admin', compact('amount', 'currency', 'tenants', 'clients', 'cables'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function tenant()
    {
        // Retrieving Tenant Details
        $tenant = Sentinel::getUser()->tenant->id;
        
        $cables = Cable::where('tenant_id', $tenant)->count();
        $clients = Client::where('tenant_id', $tenant)->count();
        $members = Client::where('tenant_id', $tenant)->get();

        return view('dashboards.tenant', compact('cables', 'clients', 'members'));
    }   
}
