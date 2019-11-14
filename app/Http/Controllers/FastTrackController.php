<?php

namespace App\Http\Controllers;

use Alert;
use Carbon;
use Redirect;
use Sentinel;

use App\Cable;
use App\Client;
use App\Region;
use App\CableType;
use App\CableAlert;
use Illuminate\Http\Request;
use App\Http\Requests\FastTrackFormRequest as FastTrackFormRequest;

class FastTrackController extends Controller
{
    public function __construct()
    {
        // Middleware
        $this->middleware('sentinel.auth');
        $this->middleware('sentinel.access:cables.create');
        $this->middleware('sentinel.access:clients.create');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Retrieving Tenant Details
        $tenant = Sentinel::getUser()->tenant->id;

        $regions = Region::all();
        $types = CableType::where('tenant_id', $tenant)->get();

        return view('fastTrack/create', compact('types', 'regions'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(FastTrackFormRequest $request)
    {
        // Retrieving Tenant Details
        $tenant = Sentinel::getUser()->tenant->id;

        // Save Client First
    	$client = new Client;

        $client->first_name         = $request->first_name;
        $client->middle_name        = $request->middle_name;
        $client->last_name          = $request->last_name;
        $client->email              = $request->email;
        $client->phone_number       = $request->phone_number;
        if ($request->phone_number_2) {
             $client->phone_number_2     = $request->phone_number_2;
        }
        $client->region_id          = $request->region;
        $client->tenant_id         	= $tenant;

        $client->save();

        $cable = new Cable;

        $cable->reference_id           = $request->reference;
        $cable->starting_date          = $request->starting_date;
        $cable->client_id              = $client->id;
        $cable->type_id                = $request->type;
        $cable->tenant_id              = $tenant;

        $cable->save();

        $expiration_date = Carbon::parse($request->starting_date)->addMonths($cable->type->duration);
        $alert_one = Carbon::parse($expiration_date)->subDays(7);
        $alert_two = Carbon::parse($expiration_date)->subDays(3);

        $alert = new CableAlert;

        $alert->alert_one           = $alert_one;
        $alert->alert_two           = $alert_two;
        $alert->expiration          = $expiration_date;
        $alert->cable_id            = $cable->id;

        $alert->save();

        Alert::success('Client & Cable Registered!');

        if ($request->finish == "Add Another") {
            return Redirect::to('fastTrack');
        }else {
            return Redirect::to('dashboard');
        }
    }
}
