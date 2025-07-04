<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = ['nom', 'duree', 'tarif'];

    public function rendezVous()
    {
        return $this->hasMany(RendezVous::class);
    }
    public function availabilities()
    {
        return $this->hasMany(ServiceAvailability::class);
    }

    /** @use HasFactory<\Database\Factories\ServiceFactory> */
    use HasFactory;
}
