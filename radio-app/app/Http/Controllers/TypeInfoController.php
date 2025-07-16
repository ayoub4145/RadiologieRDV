<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TypeInfo;
use App\Models\Section;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class TypeInfoController extends Controller
{
   public function searchSection($name)
{
    $section = Section::whereRaw('LOWER(name) = ?', [strtolower($name)])->first();

    if (!$section) {
        return response()->json(['section' => '', 'attributs' => []]);
    }

    $map = [
        'blog' => ['titre', 'contenu', 'image'],
        'cordonnee' => ['numero', 'email', 'adresse'],
        // etc.
    ];

    $attributs = $map[strtolower($section->name)] ?? [];

    $typeInfo = TypeInfo::where('section_id', $section->id)->first();

    $values = [];
    foreach ($attributs as $attr) {
        $values[$attr] = $typeInfo ? $typeInfo->$attr : null;
    }

    return response()->json([
        'section' => $section->name,
        'section_id' => $section->id,
        'attributs' => $attributs,
        'values' => $values,
    ]);
}

    public function editField($sectionId, $field)
    {
        $section = Section::findOrFail($sectionId);
        $typeInfo = TypeInfo::where('section_id', $sectionId)->first();

        if (!Schema::hasColumn('type_infos', $field)) {
            abort(404, 'Champ non valide');
        }

        $value = $typeInfo ? $typeInfo->$field : '';

        return view('sections.edit_field', compact('section', 'field', 'value'));
    }

    public function updateField(Request $request, $sectionId, $field)
    {
        $typeInfo = TypeInfo::firstOrNew(['section_id' => $sectionId]);

        if (!Schema::hasColumn('type_infos', $field)) {
            abort(404, 'Champ non valide');
        }

        $rules = [];

        if (in_array($field, ['description', 'contenu'])) {
            $rules['value'] = ['required', 'string', 'max:2000'];
        } elseif ($field === 'image') {
            $rules['value'] = ['nullable', 'image', 'max:2048'];
        } else {
            $rules['value'] = ['required', 'string', 'max:255'];
        }

        $request->validate($rules);

        if ($field === 'image' && $request->hasFile('value')) {
            if ($typeInfo->$field) {
                Storage::delete('public/uploads/' . $typeInfo->$field);
            }
            $file = $request->file('value');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('public/uploads', $filename);
            $typeInfo->$field = $filename;
        } elseif ($field !== 'image') {
            $typeInfo->$field = $request->input('value');
        }

        $typeInfo->section_id = $sectionId;
        $typeInfo->save();

        return redirect()->route('field.edit', [$sectionId, $field])
                         ->with('success', '✅ Champ mis à jour avec succès.');
    }

    public function deleteField(Request $request, $sectionId, $field)
    {
        $typeInfo = TypeInfo::where('section_id', $sectionId)->first();

        if (!$typeInfo || !Schema::hasColumn('type_infos', $field)) {
            abort(404, 'Champ non valide');
        }

        if ($field === 'image' && $typeInfo->$field) {
            Storage::delete('public/uploads/' . $typeInfo->$field);
        }

        $typeInfo->$field = null;
        $typeInfo->save();

        return response()->json(['message' => "Champ \"$field\" supprimé avec succès."]);
    }
}
