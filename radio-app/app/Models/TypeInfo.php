<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TypeInfo extends Model
{
    /** @use HasFactory<\Database\Factories\TypeInfoFactory> */
    use HasFactory;

    protected $fillable = [
        'section_id', 'titre', 'contenu', 'image',
    ];
    public function section()
    {
        return $this->belongsTo(Section::class);
    }
}
