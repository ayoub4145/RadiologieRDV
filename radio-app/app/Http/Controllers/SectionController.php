<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Section;
use Illuminate\Support\Facades\Schema;

class SectionController extends Controller
{
    public function create()
    {
        return view('sections.create_section');
    }
    public function store(Request $request)
    {
        // Validation des données du formulaire
        $request->validate([
            'section_name' => 'required|string|max:255|unique:sections,name',
            'section_description' => 'nullable|string|max:1000',
        ]);

        // Création de la section
        $section = Section::create([
            'name' => $request->section_name,
            'description' => $request->section_description,
        ]);

        return redirect()->route('admin.dashboard')->with('success', 'Section créée avec succès !');
    }
public function getTypeInfos($sectionId)
{
    // Tu peux ajouter une vérification de section si besoin
    $section = \App\Models\Section::findOrFail($sectionId);

    // Liste des colonnes que tu veux rendre disponibles comme "champs dynamiques"
    $columns = Schema::getColumnListing('type_infos');

    // Supprimer les colonnes non utiles (id, section_id, created_at...)
    $excluded = ['id', 'section_id', 'created_at', 'updated_at', 'is_active', 'ordre'];
    $filteredColumns = array_diff($columns, $excluded);

    return response()->json([
        'attributs' => array_values($filteredColumns)
    ]);
}
}
