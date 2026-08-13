<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function redirect(Request $request): RedirectResponse
    {
        // Ensure session is started and attached
        $session = $request->session();
        if (!$session->isStarted()) {
            $session->start();
        }
        
        return Socialite::driver('google')->redirect();
    }

    public function callback(Request $request): RedirectResponse
    {
        try {
            // Ensure session is started
            $session = $request->session();
            if (!$session->isStarted()) {
                $session->start();
            }
            
            $googleUser = Socialite::driver('google')->user();

            $user = User::where('email', $googleUser->email)->first();

            if ($user) {
                if (empty($user->google_id)) {
                    $user->google_id = $googleUser->id;
                    $user->avatar_url = $googleUser->avatar;
                    $user->save();
                }
                Auth::login($user, true);
                
                // Regenerate session to prevent fixation
                $request->session()->regenerate();
                
            } else {
                return redirect()->route('login')->withErrors([
                    'email' => 'Akun dengan email ini belum terdaftar. Hubungi administrator.',
                ]);
            }

            return redirect()->intended(route('dashboard'));
            
        } catch (\Exception $e) {
            // Log the error
            \Log::error('Google OAuth Error: ' . $e->getMessage());
            
            return redirect()->route('login')->withErrors([
                'email' => 'Terjadi kesalahan saat login dengan Google. Silakan coba lagi.',
            ]);
        }
    }
}
