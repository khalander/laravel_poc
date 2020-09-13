<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Socialite;
use \Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\OTPMail;
use Illuminate\Support\Facades\Cache;

class LoginController extends Controller
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

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Redirect the user to the GitHub authentication page.
     *
     * @return \Illuminate\Http\Response
     */
    public function redirectToProvider()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Obtain the user information from GitHub.
     *
     * @return \Illuminate\Http\Response
     */
    public function handleProviderCallback()
    {
        $user = Socialite::driver('google')->user();
        /* 
            1) Store the user details if not exists else just redirect
            2) if Auth is using then login using Auth::login($user)
            3) for passport, store some standard passport for all users
            4) For multiple socialite option, make param route and replace everywhere option is hard coded
        */
        return $user->name;
    }

    /**
     * Attempt to log the user into the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function attemptLogin(Request $request)
    {
        $result = $this->guard()->attempt(
            $this->credentials($request), $request->filled('remember')
        );
      
        if ($result) {
            auth()->user()->cacheTheOtp();
            auth()->user()->sentOtp(request('via'));
        }
        return $result;
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {

        auth()->user()->update([
            'isVerified' => 0
        ]);

        $this->guard()->logout();

        $request->session()->invalidate();

        return redirect('/');
    }
}
