<?php

namespace App\Http\Controllers\SuperAdmin;

use App\UniversalSearch;
use Illuminate\Http\Request;
use Froiden\Envato\Helpers\Reply;
use App\Http\Controllers\SuperAdminBaseController;

class SearchController extends SuperAdminBaseController
{
    public function __construct()
    {
        parent::__construct();
        view()->share('pageTitle', __('front.search'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $key = $request->search_key;

        if(trim($key) == ''){
            return redirect()->back();
        }

        return redirect(route('superadmin.search.show', $key));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($key) {
        $this->searchResults = UniversalSearch::where('title', 'like', '%'.$key.'%')->where('company_id', null)->where('type', 'backend')->paginate(5);
        $this->searchKey = $key;
        session()->put('searchKey', $this->searchKey);
        if(request()->ajax()){

            $view = view('superadmin.search.ajax-show',$this->data)->render();
            return Reply::dataOnly(['view' => $view]);
        }

        return view('superadmin.search.show', $this->data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
