<?php
namespace App\Http\Controllers\Front;

use App\User;
use App\Booking;
use App\Payment;
use Carbon\Carbon;
use App\Helper\Reply;
use Razorpay\Api\Api;
use App\GlobalSetting;
use App\Facades\Razorpay;
use Illuminate\Http\Request;
use App\GatewayAccountDetail;
use App\Notifications\NewBooking;
use App\PaymentGatewayCredentials;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Notifications\BookingConfirmation;
use Illuminate\Support\Facades\Notification;
use App\Http\Requests\Gateways\Razorpay\CreateOrderRequest;
use App\Http\Requests\Gateways\Razorpay\CreateAccountRequest;

class RazorPayController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->pageTitle = 'RazorPay';
    }

    /**
     * Store a details of payment with paypal.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function paymentWithRazorPay($paymentId, $return_url = null)
    {
        $response = Razorpay::fetchAndCapturePayment($paymentId);

        $razorpayPayment = $response['payment'];

        // fetch booking
        $booking = Booking::where(['order_id' => $razorpayPayment->order_id, 'user_id' => $this->user->id])->with(['company' => function($query) {
            $query->with(['gateway_account_details']);
        }])->first();

        $setting = GlobalSetting::with('currency')->first();
        $currency = $setting->currency;
        $saCredentials = PaymentGatewayCredentials::withoutGlobalScope('company')->first();

        if ($response['razorpay_response']->error_code) {

            Session::put('success',__('messages.paymentSuccessAmount') . $booking->formated_amount_to_pay);

            return Reply::redirect(route('front.payment.success', $booking->id), __('front.headings.paymentSuccess'));
        }

        $connectedAccount = $booking->company->gateway_account_details()->ofGateway('razorpay')->ofStatus('active')->ofConnectionType('connected')->first();

        // create payment
        $payment = new Payment();

        $payment->company_id = $booking->company_id;
        $payment->booking_id = $booking->id;
        $payment->currency_id = $booking->currency_id;
        $payment->customer_id = $this->user->id;
        $payment->amount = $booking->amount_to_pay;
        $payment->gateway = 'RazorPay';
        $payment->transaction_id = $razorpayPayment->id;
        $payment->paid_on = Carbon::now();
        $payment->status = 'completed';
        $payment->transfer_status = 'not_transferred';


        $commissionAmount = $saCredentials->razorpay_commission_status === 'active' ? round(($razorpayPayment->amount / 100) * $saCredentials->razorpay_commission_percentage) : 0;

        if ($connectedAccount) {
            $razorpayPayment->transfer([
                'transfers' => [
                    [
                        'account' => $connectedAccount->account_id,
                        'amount' => $razorpayPayment->amount - $commissionAmount,
                        'currency' => $currency->currency_code
                    ]
                ]
            ]);
            $payment->transfer_status = 'transferred';
        }
        $payment->commission = $commissionAmount;
        $payment->save();

        // update booking
        $booking->payment_gateway = 'RazorPay';
        $booking->payment_status = 'completed';
        $booking->save();

        // send email notifications
        $admins = User::allAdministrators()->where('company_id', $booking->company_id)->first();
        Notification::send($admins, new NewBooking($booking));

        $user = User::findOrFail($booking->user_id);
        $user->notify(new BookingConfirmation($booking));

        Session::put('success',__('messages.paymentSuccessAmount') . $booking->formated_amount_to_pay);

        if ($return_url == 'bookingPage') {

            return Reply::redirect(route('admin.bookings.index'));

        }elseif ($return_url == 'calendarPage') {

            return Reply::redirect(route('admin.calendar'));
        }
        return Reply::redirect(route('front.payment.success', $booking->id), __('front.headings.paymentSuccess'));
    }

    public function createAccount(CreateAccountRequest $request)
    {
        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'tnc_accepted' => $request->tnc_accepted == 'on' ? true : false,
            'account_details' => [
                'business_name' => $request->business_name,
                'business_type' => $request->business_type,
            ],
            'bank_account' => [
                'beneficiary_name' => $request->beneficiary_name,
                'ifsc_code' => $request->ifsc_code,
                'account_number' => $request->account_number,
            ],
        ];

        $accountDetail = Razorpay::createAccount($data);
        $details = [];
        // check status of account and save to the database
        if ($accountDetail->activation_details->status === 'activated' && $accountDetail->live) {
            $details = GatewayAccountDetail::create([
                'account_id' => $accountDetail->id,
                'connection_status' => 'connected',
                'account_status' => 'active',
                'gateway' => 'razorpay'
            ]);
        }
        else {
            return Reply::error(__('messages.accountNotConnected'));
        }

        return Reply::successWithData(__('messages.accountConnectedSuccessfully'), ['details' => $details]);
    }

    public function verifyPayment(Request $request)
    {
        $data = [
            'razorpay_payment_id' => $request->razorpay_payment_id,
            'razorpay_order_id' => $request->order_id,
            'razorpay_signature' => $request->razorpay_signature,
        ];

        $verified = Razorpay::verifyPayment($data);
        if ($verified) {
            return $this->paymentWithRazorPay($request->razorpay_payment_id, $request->return_url);
        }

        return Reply::redirect(route('front.payment.fail'), __('messages.paymentFailed'));
    }
}
