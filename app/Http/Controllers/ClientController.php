<?php

namespace App\Http\Controllers;

use Alert;
use Redirect;
use Sentinel;

use App\Client;
use App\Region;
use Illuminate\Http\Request;
use App\Http\Requests\ClientFormRequest as ClientFormRequest;
use App\Http\Requests\ClientEditFormRequest as ClientEditFormRequest;

class ClientController extends Controller
{
    public function __construct()
    {
        // Middleware
        $this->middleware('sentinel.auth');
        $this->middleware('sentinel.access:clients.create', ['only' => ['create', 'store']]);
        $this->middleware('sentinel.access:clients.view', ['only' => ['index', 'show']]);
        $this->middleware('sentinel.access:clients.update', ['only' => ['edit', 'update']]);
        $this->middleware('sentinel.access:clients.destroy', ['only' => ['destroy']]);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Retrieving Tenant Details
        $tenant = Sentinel::getUser()->tenant->id;

        $clients = Client::where('tenant_id', $tenant)->get();

        return view('clients.index', compact('clients'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $regions = Region::all();

        return view('clients.create', compact('regions'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ClientFormRequest $request)
    {
        $tenant = Sentinel::getUser()->tenant->id;

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
        $client->tenant_id          = $tenant;

        $client->save();

        Alert::success('New Client Registered!');

        return Redirect::to('clients');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $client = Client::findorfail($id);

        return view('clients.show', compact('client'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $regions = Region::all();
        $client = Client::findorfail($id);
        
        return view('clients.edit', compact('client', 'regions'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ClientEditFormRequest $request, $id)
    {
        $client = Client::findorfail($id);

        $client->first_name         = $request->first_name;
        $client->middle_name        = $request->middle_name;
        $client->last_name          = $request->last_name;
        $client->email              = $request->email;
        $client->phone_number       = $request->phone_number;
        if ($request->phone_number_2 && $request->phone_number_2 != 0) {
             $client->phone_number_2     = $request->phone_number_2;
        }
        $client->region_id          = $request->region;

        $client->save();

        Alert::success('Client Details Updated Successfully!');

        return Redirect::to('clients');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $client  = Client::findorfail($id);

        foreach($client->cables as $cable)
        {
            $cable->alert->delete();
            $cable->delete();
        }

        $client->delete();        

        Alert::success('Client Removed Successfully!');

        return Redirect::to('clients');
    }
}
