<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use PragmaRX\Google2FAQRCode\Google2FA;


class UserController extends Controller
{
    public function enable2FA(Request $request)
{
    /** @var \App\Models\User $user */

    $user = Auth::user();
    $google2fa = new Google2FA();

    $secret = $google2fa->generateSecretKey();

    $user->google2fa_secret = encrypt($secret);
    $user->save();

    $QR_Image = $google2fa->getQRCodeInline(
        config('app.name'),
        $user->email,
        $secret
    );

    return view('2fa.enable', [
        'QR_Image' => $QR_Image,
        'secret' => $secret,
    ]);
}
}
