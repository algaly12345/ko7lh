<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Country;
use App\Location;
use App\Helper\Reply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\AdminBaseController;
use App\Http\Requests\Location\StoreLocation;
use App\Http\Controllers\SuperAdminBaseController;
use App\Http\Requests\Location\ChangeLocationRequest;

class LocationController extends SuperAdminBaseController
{


    public function __construct()
    {
        parent::__construct();
        view()->share('pageTitle', __('menu.locations'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(request()->ajax()){
            $locations = Location::all();

            return datatables()->of($locations)
                ->addColumn('action', function ($row) {
                    $action = '<div class="text-right">';

                    $action.= '<a href="' . route('superadmin.locations.edit', [$row->id]) . '" class="btn btn-primary btn-circle"
                        data-toggle="tooltip" data-original-title="'.__('app.edit').'"><i class="fa fa-pencil" aria-hidden="true"></i></a>';

                    if ($row->count() != 1) {
                        $action.= ' <a href="javascript:;" class="btn btn-danger btn-circle delete-row"
                        data-toggle="tooltip" data-row-id="' . $row->id . '" data-original-title="'.__('app.delete').'"><i class="fa fa-times" aria-hidden="true"></i></a>';
                    }
                    $action .='</div>';

                    return $action;
                })
                ->editColumn('name', function ($row) {
                    return ucfirst($row->name);
                })->editColumn('country', function ($row) {
                        return $row->country?$row->country->name:'-';
                })
                ->editColumn('status', function ($row) use($locations) {
                    $active = $row->status=='active' ? 'selected' : '';
                    $inactive = $row->status!='active' ? 'selected' : '';

                    $locationOption = '<select name="location_status" class="form-control location_status" data-location-id="'.$row->id.'">';
                    $locationOption.= '<option '.$active.' value="active">Active</option>';
                    $locationOption.= '<option '.$inactive.' value="inactive">In-Active</option>';
                    $locationOption.= '</select>';

                    return $locationOption;
                })
                ->addIndexColumn()
                ->rawColumns(['action', 'status'])
                ->toJson();
        }
        return view('superadmin.location.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $countries = Country::all();
        return view('superadmin.location.create',compact('countries'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreLocation $request)
    {
        $location = new Location();
        $location->create($request->all());

        return Reply::redirect($request->redirect_url, __('messages.createdSuccessfully'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Location  $location
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Location  $location
     * @return \Illuminate\Http\Response
     */
    public function edit(Location $location)
    {
        $countries = Country::all();
        return view('superadmin.location.edit', compact('location','countries'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Location  $location
     * @return \Illuminate\Http\Response
     */
    public function update(StoreLocation $request, $id)
    {
        $location = Location::find($id);
        $location->update($request->all());

        return Reply::redirect(route('superadmin.locations.index'), __('messages.updatedSuccessfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Location  $location
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Location::destroy($id);
        return Reply::success(__('messages.recordDeleted'));
    }


    public function changeStatus(ChangeLocationRequest $request)
    {
        $location = Location::findOrFail($request->location_id);
        $location->status = $request->location_status;
        $location->save();

        Artisan::call('cache:clear');

        return Reply::success(__('messages.locationStatusChangedSuccessfully'));
    }











}
