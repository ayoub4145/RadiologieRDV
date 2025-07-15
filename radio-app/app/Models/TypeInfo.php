<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TypeInfo extends Model
{
    /** @use HasFactory<\Database\Factories\TypeInfoFactory> */
    use HasFactory;
    public function section()
    {
        return $this->belongsTo(Section::class);
    }
}
