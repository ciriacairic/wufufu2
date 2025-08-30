<?php

namespace App\Http\Controllers;

use App\Libraries\Helpers\JwtHelper;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;

class LoginController extends Controller
{
    public function loginToSpotify()
    {
        return Socialite::driver('spotify')->scopes([
            'user-read-email',
            'playlist-read-private',
            'streaming',
            'user-read-playback-state',
            'user-modify-playback-state'
        ])->redirect();
    }

    public function callbackLoginFromSpotify(Request $request)
    {
        $spotifyProvider = Socialite::driver('spotify');

        try {
            $spotifyUser = $spotifyProvider->user();
        } catch (InvalidStateException $exception) {
            Log::warning($exception);
            return redirect('/');
        } catch (\Throwable $exception) {
            if ($exception->getCode() == 400) {
                Log::warning($exception);
                return redirect("/");
            }
            Log::error($exception);
            return view('error', [
                'error_msg' => $request->input('error_description') ?? "Response from Spotify didn't provide the authenticated user information"
            ]);
        }

        $accessToken = $spotifyUser->token;
        $expiresAt = now()->addSeconds($spotifyUser->expiresIn);
        $refreshToken = $spotifyUser->refreshToken;

        session([
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
            'expires_at' => $expiresAt,
        ]);

        User::upsert([
            'email' => $spotifyUser->email,
            'name' => $spotifyUser->name,
            'avatar' => $spotifyUser->avatar,
            'profile_url' => $spotifyUser->user['external_urls']['spotify'],
            'spotify_id' => $spotifyUser->id,
        ], uniqueBy: ['email']);

        $user = User::where('email', $spotifyUser->email)->first();

            Auth::login($user);

        if ($request->expectsJson()) {
            // Scenario when the user leaves the app open, then timeout and within the SPA requests an API request,
            // so in this scenario we shall redirect to home,otherwise it will try to redirect to the API endpoint
            return redirect('/');
        }

        return Redirect::intended('/');
    }

//
//    public function logout(Request $request)
//    {
//        Auth::guard()->logout();
//        $request->session()->flush();
//        $azureLogoutUrl = Socialite::driver('azure')->getLogoutUrl(route('login'));
//        return redirect($azureLogoutUrl);
//    }

}
