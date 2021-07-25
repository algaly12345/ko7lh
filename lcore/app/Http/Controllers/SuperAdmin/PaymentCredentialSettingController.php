<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Helper\Reply;
use App\Http\Requests\Payment\UpdateCredentialSetting;
use App\PaymentGatewayCredentials;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\SuperAdminBaseController;

class PaymentCredentialSettingController extends SuperAdminBaseController
{
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
    public function update(UpdateCredentialSetting $request, $id)
    {
        if($request->razorpay_status != 'active' && $request->stripe_status != 'active' && $request->paypal_status != 'active' && $request->offline_payment != 1){
            return Reply::error(__('messages.paymentActiveRequired'));
        }

        $credential = PaymentGatewayCredentials::first();

        $credential->offline_payment = $request->offline_payment;
        $credential->show_payment_options = $request->show_payment_options;

        $credential->stripe_client_id = $request->stripe_client_id;
        $credential->stripe_secret = $request->stripe_secret;
        $credential->stripe_webhook_secret = $request->stripe_webhook_secret;
        $credential->stripe_status = $request->stripe_status;
        $credential->stripe_commission_status = $request->stripe_commission_status;
        $credential->stripe_commission_percentage = $request->stripeCommmissionAmt;

        $credential->razorpay_key = $request->razorpay_key;
        $credential->razorpay_secret = $request->razorpay_secret;
        $credential->razorpay_webhook_secret = $request->razorpay_webhook_secret;
        $credential->razorpay_status = $request->razorpay_status;
        $credential->razorpay_commission_status = $request->razorpay_commission_status;
        $credential->razorpay_commission_percentage = $request->razorCommmissionAmt;

        $credential->save();

        return Reply::success(__('messages.updatedSuccessfully'));
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
