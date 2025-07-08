<?php

namespace App\Http\Controllers;

use App\Notifications\TwoFactorCodeNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use PragmaRX\Google2FALaravel\Google2FA;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Writer;

class TwoFactorController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        $google2fa = app(Google2FA::class);

        if (!$user->google2fa_secret) {
            /** @var \App\Models\User $user */

            $user->google2fa_secret = $google2fa->generateSecretKey();
            $user->save();
        }

        $QR_Image = $this->generateQrCode($user->email, $user->google2fa_secret);

        return view('auth.2fa.enable', [
            'QR_Image' => $QR_Image,
            'secret' => $user->google2fa_secret,
        ]);
    }

    private function generateQrCode($email, $secret)
    {
        $google2fa = app(Google2FA::class);
        $qrUrl = $google2fa->getQRCodeUrl(
            config('app.name'),
            $email,
            $secret
        );

        $writer = new Writer(
            new ImageRenderer(
                new RendererStyle(200),
                new SvgImageBackEnd()
            )
        );

        return $writer->writeString($qrUrl);
    }

    public function enable(Request $request)
    {
    /** @var \App\Models\User $user */

        $request->validate(['code' => 'required|digits:6']);

        $user = Auth::user();
        $google2fa = app(Google2FA::class);

        if ($google2fa->verifyKey($user->google2fa_secret, $request->code)) {
            /** @var \App\Models\User $user */

            $user->two_factor_enabled = true;
            $user->save();

            session(['2fa_passed' => true]);

            return redirect()->route('dashboard')->with('success', '2FA activée avec succès.');
        }

        return back()->withErrors(['code' => 'Le code est invalide.']);
    }

    public function disable()
    {
    /** @var \App\Models\User $user */

        $user = Auth::user();

        $user->two_factor_enabled = false;
        $user->google2fa_secret = null;
        $user->save();

        return redirect()->route('dashboard')->with('success', '2FA désactivée.');
    }

    public function show2faSetup()
    {
         /** @var \App\Models\User $user */
        $user = Auth::user();
        $google2fa = app(Google2FA::class);

        if (!$user->google2fa_secret) {
            $user->google2fa_secret = $google2fa->generateSecretKey();
            $user->save();
        }

        $qrCode = $google2fa->getQRCodeInline(
            config('app.name'),
            $user->email,
            $user->google2fa_secret
        );

        return view('auth.2fa.setup', [
            'qrCode' => $qrCode,
            'secret' => $user->google2fa_secret,
        ]);
    }
}
