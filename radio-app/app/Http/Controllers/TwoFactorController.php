<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PragmaRX\Google2FAQRCode\Google2FA;
use App\Notifications\TwoFactorCodeNotification;

class TwoFactorController extends Controller
{
//     public function resend(Request $request){

//         Auth::user()->regenerateTwoFactorCode();
//         Auth::user()->notify(new TwoFactorCodeNotification());
//         return back()->with('status', 'Un nouveau code a été envoyé à votre adresse e-mail.');
//     }

public function verify(Request $request)
{
 return view('2FA.verify');

}
//  public function verifyCode(Request $request)
//     {
//         $request->validate([
//             'code' => 'required|digits:6',
//         ]);

//         if ($request->code == Auth::user()->two_factor_code) {
//             session()->put('2fa_passed', true);
//             return redirect()->intended('/dashboard');
//         }

//         return back()->withErrors(['code' => 'Le code est incorrect.']);
//     }

    public function resend(Request $request)
    {
        /** @var \App\Models\User $user */
            $user = Auth::user();

            if ($user && method_exists($user, 'regenerateTwoFactorCode')) {
                $user->regenerateTwoFactorCode();
                $user->notify(new TwoFactorCodeNotification());
            }

        return back()->with('status', 'Un nouveau code a été envoyé à votre adresse e-mail.');
    }

    public function verifyCode(Request $request)
{

       $request->validate([
        'code' => 'required|digits:6',
    ]);
    /** @var \App\Models\User $user */
    $user = Auth::user();

    if (
        $user->two_factor_code === $request->code &&
        now()->lt($user->two_factor_expires_at)
    ) {
        $user->resetTwoFactorCode();
        session(['2fa_passed' => true]); // très important

        return redirect()->intended('/dashboard');
    }

    return back()->with('error', 'Code invalide ou expiré.');

}
}
