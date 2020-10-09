<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

use Laravel\Socialite\Facades\Socialite;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

use Illuminate\Http\Request;

use App\Models\User;
use App\Models\SocialProfile;
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

    /**
     * Redirect the user to the GitHub authentication page.
     *
     * @return \Illuminate\Http\Response
     */
    public function redirectToProvider($driver)
    {
        $drivers = ['facebook', 'google', 'github'];
        if(in_array($driver, $drivers))
        {
            return Socialite::driver($driver)->redirect();
        }

        return redirect()->route('login')->with('info', $driver . ' no es una aplicación valida para poder logearse');
    }

    /**
     * Obtain the user information from GitHub.
     *
     * @return \Illuminate\Http\Response
     */
    public function handleProviderCallback(Request $request, $driver)
    {
        if($request->get('error')){
            return redirect()->route('login')->with('info', $driver . ' devolvio un error');
        }
        $user_socialite = Socialite::driver($driver)->user();

        $user = User::firstOrCreate([
            'email' => $user_socialite->getEmail()
        ],
        [
            'name' => $user_socialite->getName(),
            'email' => $user_socialite->getEmail(),
            'password' => Hash::make(Str::random(20))
        ]);

        $social_profile = SocialProfile::firstOrCreate([
            'social_id'=> $user_socialite->getId(),
            'social_name'=> $driver
        ],
        [
            'user_id'=> $user->id,
            'social_id'=> $user_socialite->getId(),
            'social_name'=> $driver,
            'social_avatar' => $user_socialite->getAvatar()

        ]);

        Auth::login($user);

        return redirect()->route('home');
    }
}
