<?php

namespace App\Http\Controllers;

use Alert;
use Redirect;
use Sentinel;

use App\CableType;
use Illuminate\Http\Request;
use App\Http\Requests\CableTypeFormRequest as CableTypeFormRequest;
use App\Http\Requests\CableTypeEditFormRequest as CableTypeEditFormRequest;

class TypeController extends Controller
{
    public function __construct()
    {
        // Middleware
        $this->middleware('sentinel.auth');
        $this->middleware('sentinel.access:settings.access');
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

        $types = CableType::where('tenant_id', $tenant)->get();

        return view('settings.tenant.types.index', compact('types'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('settings.tenant.types.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CableTypeFormRequest $request)
    {
        // Retrieving Tenant Details
        $tenant = Sentinel::getUser()->tenant->id;

        $type = new CableType;

        $type->name         = $request->name;
        $type->amount       = $request->amount;
        $type->duration     = $request->duration;
        $type->tenant_id    = $tenant;

        $type->save();

        Alert::success('New Cable Type Registered!');

        return Redirect::to('types');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $type = CableType::findorfail($id);

        return view('settings.tenant.types.edit', compact('type'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CableTypeEditFormRequest $request, $id)
    {
        $type = CableType::findorfail($id);

        $type->name         = $request->name;
        $type->amount       = $request->amount;
        $type->duration     = $request->duration;

        $type->save();

        Alert::success('Cable Type Edited Successfully!');

        return Redirect::to('types');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $type = CableType::findorfail($id);

        if ($type->cables->count() > 0) {
            Alert::error('Cable Type Has Policies Defined. Please Remove The Cables First!', 'Error')->persistent('Close');

            return redirect()->back();
        }

        $type->delete();

        Alert::success('Cable Type Removed Successfully!');

        return Redirect::to('types');
    }
}
