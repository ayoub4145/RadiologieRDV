<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use PragmaRX\Google2FAQRCode\Google2FA;


class UserController extends Controller
{
    /**
 * @OA\Get(
 *     path="/user/enable2fa",
 *     summary="Afficher la page d'activation 2FA",
 *     description="Génère une clé secrète 2FA, crée un QR code inline et retourne la vue d'activation pour l'utilisateur connecté.",
 *     tags={"Utilisateur"},
 *     security={{"sessionAuth": {}}},
 *     @OA\Response(
 *         response=200,
 *         description="Page d'activation 2FA affichée avec QR code",
 *         @OA\Response(
 *             response=401,
 *             description="Utilisateur non authentifié"
 *         )
 *     )
 * )
 */
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
