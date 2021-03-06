<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });


Route::group(['middleware' => ['api'], 'namespace' => 'Api'], function () {
    Route::get('get-main-services', 'BackendController@index');
    Route::get('get-all-categories', 'BackendController@getCategories');
    Route::get('get-service-by-slug', 'BackendController@vendorPage');
    Route::get('get-service-by-cate', 'BackendController@AllServices');



    //Users


});

Route::post('login', 'ApiAuthController@login');
Route::post('create', 'UserController@register');

