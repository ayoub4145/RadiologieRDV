<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Visiteur extends Model
{
    protected $fillable=['name','telephone','email'];
    
    public function rendezVous()
    {
        return $this->hasMany(RendezVous::class);
    }
}
