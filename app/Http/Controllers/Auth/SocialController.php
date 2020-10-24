<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Fortify;
use Laravel\Socialite\Facades\Socialite;

/*
|--------------------------------------------------------------------------
| SocialController
|--------------------------------------------------------------------------
|
| Parte de las pruebas de integración de Socialite con Fortify para ver
| como loguear a un usuario una vez autenticado por una red social.
| En este ejemplo utilizaremos OAuth de Facebook
|
*/

class SocialController extends Controller
{
    /**
     * Redirect the user to the Facebook authentication page.
     *
     * @return \Illuminate\Http\Response
     */
    public function redirectToFacebook()
    {
        return Socialite::driver('facebook')->redirect();
    }    

    /**
     * Obtain the user information from Facebook.
     *
     * @return \Illuminate\Http\Response
     */
public function handleFacebookCallback()
{
    $fb_user = Socialite::driver('facebook')->user();

    $user = User::where('email', $fb_user->getEmail())->firstOrFail();

    Auth::login($user);
    return redirect('/dashboard');
}     
}
