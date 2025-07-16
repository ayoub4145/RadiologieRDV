<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RendezVous;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use App\Models\TypeInfo;
class AdminController extends Controller
{
    /**
 * @OA\Get(
 *     path="/admin/dashboard",
 *     summary="Afficher les rendez-vous urgents pour l'administrateur",
 *     description="Retourne une liste de tous les rendez-vous urgents avec les utilisateurs et visiteurs associés. Accessible uniquement aux administrateurs connectés.",
 *     tags={"Admin"},
 *     security={{"sessionAuth": {}}},
 *     @OA\Response(
 *         response=200,
 *         description="Liste des rendez-vous urgents",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(
 *                 @OA\Property(property="id", type="integer"),
 *                 @OA\Property(property="date_heure", type="string", format="date-time"),
 *                 @OA\Property(property="is_urgent", type="boolean"),
 *                 @OA\Property(property="user", type="object",
 *                     @OA\Property(property="id", type="integer"),
 *                     @OA\Property(property="name", type="string"),
 *                     @OA\Property(property="email", type="string")
 *                 ),
 *                 @OA\Property(property="visiteur", type="object",
 *                     @OA\Property(property="id", type="integer"),
 *                     @OA\Property(property="nom_visiteur", type="string"),
 *                     @OA\Property(property="email", type="string"),
 *                     @OA\Property(property="telephone", type="string")
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=302,
 *         description="Redirection vers la page de connexion si l'utilisateur n'est pas admin ou non connecté"
 *     )
 * )
 */


    public function index()
    {
        // Vérification si l'utilisateur est authentifié et a le rôle d'administrateur
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return redirect('/login')->with('error', 'Accès non autorisé');
        }
        else{
            $rdvUrgents = RendezVous::with(['user', 'visiteur'])
                ->where('is_urgent', true)
                ->get();
            $sections = \App\Models\Section::all();
            $type_infos=\App\Models\TypeInfo::all();
            //dd($section);
        }
        // Logique pour afficher le tableau de bord de l'administrateur
        return view('admin.dashboard',compact('rdvUrgents', 'sections','type_infos'));
    }
    public function storeSectionData(Request $request)
{
    $validated = $request->validate([
        'section' => 'required|exists:sections,id',
        'inputs' => 'array',
        'inputs.titre' => 'nullable|string|max:255',
        'inputs.description' => 'nullable|string|max:1000',
        'inputs.contenu' => 'nullable|string|max:1000',
        'inputs.email' => 'nullable|email',
        'inputs.numero' => 'nullable|string|max:20',
        'inputs.lien' => 'nullable|url',
        // 'inputs.ordre' => 'nullable|integer',
        'inputs.image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
    ]);

    $data = $validated['inputs'] ?? [];
    $data['section_id'] = $request->input('section');

    // gestion image
    if ($request->hasFile('inputs.image')) {
        $path = $request->file('inputs.image')->store('images', 'public');
        $data['image'] = $path;
    }

    TypeInfo::create($data);

    return redirect()->back()->with('success', 'Données enregistrées avec succès.');
}



public function searchSection($name)
{
    $section = \App\Models\Section::whereRaw('LOWER(name) = ?', [strtolower($name)])->first();

    if (!$section) {
        return response()->json([
            'section' => $name,
            'attributs' => []
        ]);
    }

    // Même logique que getTypeInfos
    $typeInfo = \App\Models\TypeInfo::where('section_id', $section->id)->first();
    $columns = Schema::getColumnListing('type_infos');
    $excluded = ['id', 'section_id', 'created_at', 'updated_at'];

    $attributs = [];

    if ($typeInfo) {
        foreach ($columns as $column) {
            if (!in_array($column, $excluded) && !is_null($typeInfo->$column)) {
                $attributs[] = $column;
            }
        }
    } else {
        $attributs = array_values(array_diff($columns, $excluded));
    }

    return response()->json([
        'section' => $section->name,
        'attributs' => $attributs,
    ]);
}

public function show_add_admin_form(){
    return view('admin.add_admin');
}

public function add_other_admin(Request $request)
{
    $request->validate([
        'admin_name' => 'required|string|max:255',
        'admin_email' => 'required|email|unique:users,email',
        'admin_phone'=> 'required|string|max:15',
        'admin_password' => 'required|string|min:8',
    ]);

    // Création de l'utilisateur admin
    $nv_admin = \App\Models\User::create([
        'name' => $request->admin_name,
        'email' => $request->admin_email,
        'phone_number'=>$request->admin_phone,
        'role' => 'admin', // Assurez-vous que le rôle est défini sur admin
        'password' => bcrypt($request->admin_password),
    ]);
    // dd($nv_admin);
    return redirect()->route('admin.dashboard')
                        ->with('success', 'Nouvel administrateur ajouté avec succès !')
                        ->with('new_admin', $nv_admin);;

}

}
