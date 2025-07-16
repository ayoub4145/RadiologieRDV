<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Section;
use Illuminate\Support\Facades\Schema;
use App\Models\TypeInfo;

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
    $section = \App\Models\Section::findOrFail($sectionId);

    // On regarde le premier enregistrement existant pour cette section
    $typeInfo = \App\Models\TypeInfo::where('section_id', $sectionId)->first();

    // Si aucun enregistrement, on retourne tous les champs sauf les colonnes système
    if (!$typeInfo) {
        $columns = Schema::getColumnListing('type_infos');
        $excluded = ['id', 'section_id', 'created_at', 'updated_at','numero','email'];
        $attributs = array_values(array_diff($columns, $excluded));
    } else {
        // Sinon, on retourne uniquement les colonnes non nulles
        $columns = Schema::getColumnListing('type_infos');
        $excluded = ['id', 'section_id', 'created_at', 'updated_at', 'is_active'];
        $attributs = [];

        foreach ($columns as $column) {
            if (!in_array($column, $excluded) && !is_null($typeInfo->$column)) {
                $attributs[] = $column;
            }
        }
    }

    return response()->json([
        'section' => $section->name,
        'attributs' => $attributs,
    ]);
}
    public function destroy($sectionId)
    {
        $section = Section::findOrFail($sectionId);

        $typeInfo = TypeInfo::where('section_id', $sectionId)->first();
        if ($typeInfo) {
            $typeInfo->delete();
        }

        $section->delete();

        return response()->json(['message' => 'Section supprimée avec succès.']);
    }

}
