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
use Illuminate\Support\Facades\Log;
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
/**
 * @OA\Post(
 *     path="/2fa/enable",
 *     summary="Activer la validation à deux facteurs",
 *     description="Valide le code 6 chiffres envoyé par l'utilisateur et active 2FA.",
 *     tags={"2FA"},
 *     security={{"sessionAuth": {}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"code"},
 *             @OA\Property(property="code", type="string", example="123456")
 *         )
 *     ),
 *     @OA\Response(
 *         response=302,
 *         description="Redirection vers le dashboard avec succès"
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Code invalide ou erreur de validation"
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Non authentifié"
 *     )
 * )
 */
    public function enable(Request $request)
    {
        try {
            /** @var \App\Models\User $user */
            $request->validate(['code' => 'required|digits:6']);

            $user = Auth::user();
            $google2fa = app(Google2FA::class);

            if ($google2fa->verifyKey($user->google2fa_secret, $request->code)) {
                /** @var \App\Models\User $user */
                $user->two_factor_enabled = true;
                $user->save();

                // Marquer comme validé pour cette session
                session(['2fa_passed' => true]);

                return redirect()->route('dashboard')->with('success', '2FA activée avec succès.');
            }

            return back()->withErrors(['code' => 'Le code est invalide.']);
        } catch (\Exception $e) {
            Log::error('Erreur 2FA enable: ' . $e->getMessage());
            return back()->withErrors(['code' => 'Erreur lors de l\'activation de 2FA.']);
        }
    }
/**
 * @OA\Post(
 *     path="/2fa/disable",
 *     summary="Désactiver la validation à deux facteurs",
 *     description="Désactive 2FA pour l'utilisateur authentifié.",
 *     tags={"2FA"},
 *     security={{"sessionAuth": {}}},
 *     @OA\Response(
 *         response=302,
 *         description="Redirection vers le dashboard avec succès"
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Non authentifié"
 *     )
 * )
 */
    public function disable()
    {
        try {
            /** @var \App\Models\User $user */
            $user = Auth::user();

            $user->two_factor_enabled = false;
            $user->google2fa_secret = null;
            $user->save();

            // Clear 2FA session
            session()->forget('2fa_passed');

            return redirect()->route('dashboard')->with('success', '2FA désactivée.');
        } catch (\Exception $e) {
            Log::error('Erreur 2FA disable: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Erreur lors de la désactivation de 2FA.']);
        }
    }
/**
 * @OA\Get(
 *     path="/2fa/setup",
 *     summary="Afficher la page de configuration 2FA",
 *     description="Retourne la vue avec QR code et clé secrète pour configurer Google Authenticator.",
 *     tags={"2FA"},
 *     security={{"sessionAuth": {}}},
 *     @OA\Response(
 *         response=200,
 *         description="Page de configuration 2FA affichée"
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Non authentifié"
 *     )
 * )
 */
    public function show2faSetup()
    {
        try {
            /** @var \App\Models\User $user */
            $user = Auth::user();
            $google2fa = app(Google2FA::class);

            // Generate secret if not exists
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
        } catch (\Exception $e) {
            Log::error('Erreur 2FA setup: ' . $e->getMessage());
            return redirect()->route('dashboard')->withErrors(['error' => 'Erreur lors du chargement de la configuration 2FA.']);
        }
    }
}
