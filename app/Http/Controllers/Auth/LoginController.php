<?php

namespace App\Http\Controllers\Auth;

use App\Company;
use App\Http\Controllers\FrontBaseController;
use App\User;
use Froiden\Envato\Traits\AppBoot;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

class LoginController extends FrontBaseController
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers, AppBoot;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/account/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        view()->share('pageTitle', __('email.loginAccount'));
        $this->middleware('guest')->except('logout');
    }

    public function showLoginForm()
    {
        if (!$this->isLegal()) {
            return redirect('verify-purchase');
        }

        if (!session()->has('errors')) {
            session()->put('url.encoded', url()->previous());
        }

        return view('auth.login');
    }

    protected function attemptLogin(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        if ($user && $user->is_admin) {
            if ($user->company->verified == 'yes') {
                return $this->guard()->attempt(
                    $this->credentials($request), $request->filled('remember')
                );
            }
        }else {

            return $this->guard()->attempt(
                $this->credentials($request), $request->filled('remember')
            );
        }
    }

    /**
     * The user has been authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {
        if ($user->is_superadmin) {
            return redirect()->route('superadmin.dashboard');
        }
        if ($user->is_admin && $user->company->verified == 'yes') {
            return redirect()->route('admin.dashboard');
        }
        if ($user->is_employee) {
            return redirect()->route('admin.dashboard');
        }
        return redirect(session()->get('url.encoded'));
    }

    /**
     * The user has logged out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    protected function loggedOut(Request $request)
    {
        session()->forget('url.encoded');

        return redirect(url()->previous());
    }
}
