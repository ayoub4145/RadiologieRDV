<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
 * @OA\Post(
 *     path="/register",
 *     summary="Enregistrement d'un nouveau patient",
 *     tags={"Authentification"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"name", "email", "password", "password_confirmation"},
 *             @OA\Property(property="name", type="string", example="Jean Dupont"),
 *             @OA\Property(property="email", type="string", format="email", example="jean@example.com"),
 *             @OA\Property(property="password", type="string", format="password", example="password123"),
 *             @OA\Property(property="password_confirmation", type="string", format="password", example="password123")
 *         )
 *     ),
 *     @OA\Response(
 *         response=302,
 *         description="Redirection vers le dashboard après enregistrement"
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Données invalides"
 *     )
 * )
 */

    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }


    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */

    /**
     * @OA\Post(
     *     path="/register",
     *     summary="Enregistrement d'un nouveau patient",
     *     tags={"Authentification"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "email", "password", "password_confirmation", "phone_number"},
     *             @OA\Property(property="name", type="string", example="Jean Dupont"),
     *             @OA\Property(property="email", type="string", format="email", example="jean@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123"),
     *             @OA\Property(property="password_confirmation", type="string", format="password", example="password123"),
     *             @OA\Property(property="phone_number", type="string", example="0612345678")
     *         )
     *     ),
     *     @OA\Response(
     *         response=302,
     *         description="Redirection vers le dashboard après enregistrement"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Données invalides"
     *     )
     * )
     */
    public function store(Request $request): RedirectResponse
    {

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'phone_number' => ['required', 'string','max:10'], // Validation pour le numéro de téléphone
        ]);


        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'role' => 'patient', // Assuming '1' is the ID for the 'patient' role
            'password' => Hash::make($request->password),
            'phone_number' => $request->phone_number, // Enregistrement du numéro de téléphone
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }

     /**
     * @OA\Post(
     *     path="/register/medecin",
     *     summary="Enregistrement d'un nouveau médecin",
     *     tags={"Authentification"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "email", "password", "password_confirmation", "phone_number"},
     *             @OA\Property(property="name", type="string", example="Dr. Marie Curie"),
     *             @OA\Property(property="email", type="string", format="email", example="marie.curie@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123"),
     *             @OA\Property(property="password_confirmation", type="string", format="password", example="password123"),
     *             @OA\Property(property="phone_number", type="string", example="0612345678")
     *         )
     *     ),
     *     @OA\Response(
     *         response=302,
     *         description="Redirection vers la route de redirection par rôle"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Données invalides"
     *     )
     * )
     */

            public function storeMedecin(Request $request): RedirectResponse
        {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|confirmed|min:8',
                'phone_number'=>'required|string|max:10', // Validation pour le numéro de téléphone
            ]);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'medecin', // <-- assigné explicitement
                'phone_number' => $request->phone_number, // Enregistrement du numéro de téléphone
            ]);

            Auth::login($user);

            return redirect()->route('redirect.by.role')
                ->with('status', 'Medecin registered successfully!');
        }

}
