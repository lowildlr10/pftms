<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\EmpAccount;

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
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function username()
    {
        return 'username';
    }

    /**
     * Get the needed authorization credentials from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function authenticate(Request $request)
    {
        //return $request->only($this->username(), 'password');
        return ['username' => $request->{$this->username()},
                'password' => $request->password,
                'is_active' => 'y'];
    }

    /**
     * Get the needed authorization credentials from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function credentials(Request $request)
    {
        return ['username' => $request->{$this->username()},
                'password' => $request->password,
                'is_active' => 'y'];
    }

    /**
     * Attempt to log the user into the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function attemptLogin(Request $request)
    {
        $user = new EmpAccount;
        $username = $request->{$this->username()};
        $clientIP = $request->getClientIp();
        $isAuthenticated = $this->guard()->attempt(
            $this->credentials($request), $request->filled('remember')
        );
        $msg = "Login authenticated by the system [$username@$clientIP].";

        if (!$isAuthenticated) {
            $msg = "Attempting login with invalid credentials [$username@$clientIP] [pw: $request->password].";
        }

        $user->log($request, $msg);

        return $isAuthenticated;
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
        $fullname = $user->getEmployee($user->id)->name;
        $msg = "$fullname successfully logged in.";

        $user->log($request, $msg);
        $user->last_login = Carbon::now();
        $user->save();

        $countUserNotif = count($user->unreadNotifications);
        $msg = $countUserNotif > 0 ?
            "Welcome back!<br><strong>$fullname</strong>.<br><br>
            <i class='fas fa-info-circle'></i>
            You have <strong>$countUserNotif</strong> notification" . ($countUserNotif > 1 ? 's.' : '.'):
            "Welcome back!<br><strong>$fullname</strong>.";
        $request->session()->flash('login_msg', $msg);
        $request->session()->flash('user_avatar', $user->avatar);
        $request->session()->flash('user_gender', $user->gender);
    }
}
