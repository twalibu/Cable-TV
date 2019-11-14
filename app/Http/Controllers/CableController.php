<?php

namespace App\Http\Controllers;

use Alert;
use Carbon;
use Redirect;
use Sentinel;

use App\Cable;
use App\Client;
use App\CableType;
use App\CableAlert;
use Illuminate\Http\Request;
use App\Http\Requests\CableFormRequest as CableFormRequest;
use App\Http\Requests\CableEditFormRequest as CableEditFormRequest;

class CableController extends Controller
{
    public function __construct()
    {
        // Middleware
        $this->middleware('sentinel.auth');
        $this->middleware('sentinel.access:cables.create', ['only' => ['create', 'store']]);
        $this->middleware('sentinel.access:cables.view', ['only' => ['index', 'show']]);
        $this->middleware('sentinel.access:cables.update', ['only' => ['edit', 'update']]);
        $this->middleware('sentinel.access:cables.destroy', ['only' => ['destroy']]);
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

        $cables = Cable::where('tenant_id', $tenant)->get();

        return view('cables.index', compact('cables'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Retrieving Tenant Details
        $tenant = Sentinel::getUser()->tenant->id;

        $clients = Client::where('tenant_id', $tenant)->get();
        $types = CableType::where('tenant_id', $tenant)->get();

        return view('cables/create', compact('clients', 'types'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CableFormRequest $request)
    {
        // Retrieving Tenant Details
        $tenant = Sentinel::getUser()->tenant->id;

        $cable = new Cable;

        $cable->reference_id           = $request->reference;
        $cable->starting_date          = $request->starting_date;
        $cable->client_id              = $request->client;
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

        Alert::success('New Cable Registered!');

        return Redirect::to('cables');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $cable = Cable::findorfail($id);

        return view('cables.show', compact('cable'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $cable = Cable::findorfail($id);
        
        // Retrieving Tenant Details
        $tenant = Sentinel::getUser()->tenant->id;

        $clients = Client::where('tenant_id', $tenant)->get();
        $types = CableType::where('tenant_id', $tenant)->get();

        return view('cables.edit', compact('cable', 'clients', 'types'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CableEditFormRequest $request, $id)
    {
        $cable = Cable::findorfail($id);

        $cable->reference_id           = $request->reference;
        $cable->starting_date          = $request->starting_date;
        $cable->client_id              = $request->client;
        $cable->type_id                = $request->type;
        
        $cable->save();

        $expiration_date = Carbon::parse($request->starting_date)->addMonths($cable->type->duration);
        $alert_one = Carbon::parse($expiration_date)->subDays(7);
        $alert_two = Carbon::parse($expiration_date)->subDays(3);

        $alert = CableAlert::where('cable_id', $cable->id)->first();;

        $alert->alert_one           = $alert_one;
        $alert->alert_two           = $alert_two;
        $alert->expiration          = $expiration_date;
        $alert->cable_id            = $cable->id;

        $alert->save();

        Alert::success('Cable Details Updated Successfully!');

        return redirect()->action(
            'CableController@show', ['id' => $cable->id]
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $cable  = Cable::findorfail($id);

        $cable->alert->delete();
        
        $cable->delete();

        Alert::success('Cable Removed Successfully!');

        return Redirect::to('cables');
    }
}
